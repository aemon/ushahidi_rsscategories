<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mark Controller
 *
**/

class Rsscategories_Controller extends Controller {
	
	public function __construct()
    {
    	$this->table_prefix = Kohana::config('database.default.table_prefix');
        $this->title = Kohana::config('rsscategories.rss_title');
        $this->description = Kohana::config('rsscategories.rss_description');
        
        $this->need_translate = Kohana::config('rsscategories.need_translate');
        if ($this->need_translate ==true){
            $this->langs = Kohana::config('rsscategories.to_translator_lang');
            $this->default_lang = Kohana::config('rsscategories.original_lang');
            $this->translator_fields = Kohana::config('rsscategories.to_translator_fields');
            $this->table_alias = Kohana::config('rsscategories.table_alias');
        }
    
    }
    
	
	function index()
	{
		
	}
    
    function get($category='all', $lang='ru'){
        $db = Database::instance(); 
    
        if ((empty($category))||($category=='all')){
            $category = 0;        
        }
        $category = intval($category);
        $result = '<?xml version="1.0" encoding="UTF-8"?> '.
                    '<rss version="2.0">';
                    
        $db = Database::instance(); 
        if (!empty($category)){  
            $query = "SELECT c.id, c.category_title as title, c.category_description as description ".
                    " FROM ".$this->table_prefix."category as c ".
                    " WHERE c.category_visible=1 AND id=".$category;
            $query = $db->query($query);
            $category = $query->result_array(FALSE);     
            if (empty($category)){
                //$this->PushError('rss','There isnt category');
                throw new Kohana_404_Exception();
                return;     
            }  
        }
        
        $query = 'SELECT i.id as id, i.incident_title as title,
                 i.incident_description as description, incident_dateadd as dateadd '.
                 ' FROM incident as i '.
                 ' JOIN incident_category as ci ON i.id=ci.incident_id ';
        if ($this->need_translate ==true){
            if ((empty($lang))||(empty($this->langs[$lang]))){
                $lang =  Kohana::config('rsscategories.original_lang');
            }
            if ($lang!=$this->default_lang){
                $i=1; 
                foreach ($this->translator_fields as $original=>$translate){
            
                     // $query = str_ireplace($this->table_alias.".".$original, "IFNULL (translator".$i.".value, ".$this->table_alias.".".$original.") ",$query);
                      $query = str_ireplace($this->table_alias.".".$original, "IF ((translator".$i.".value IS NULL OR translator".$i.".value='')  ,".$this->table_alias.".".$original.", translator".$i.".value ) ",$query);
                      $query .= " LEFT JOIN translator as translator".$i." ON translator".$i.".element_id=".$this->table_alias.".id AND translator".$i.".field='".$translate."'"; 
                    $i++;
                                            
                 }
            }
        }
             
        $query.=' WHERE i.incident_active = 1 ';
                
        if (!empty($category)){
            $category = $category[0];
            $query.=' AND ci.category_id  IN '.
            '(SELECT cs.id FROM category as cs '.
            ' WHERE cs.parent_id='.$category['id'].' OR cs.id='.$category['id'].')';
            
            
        }
            $query.=' GROUP BY i.id ORDER BY incident_dateadd DESC ';
            if (Kohana::config('rsscategories.items_limit')!==0){
                $query.=' LIMIT '.Kohana::config('rsscategories.items_limit');
            }
           // var_dump($query);  
              $query = $db->query($query);
              $items = $query->result(FALSE);
              $result.= $this->create_rss_chanel($items,$category
              , $this->title,$this->description);
              
             
        $result.='</rss>';
        echo $result;
    }
    
    public function create_rss_chanel($items=array(), $category=array(), $title='', $description=''){
        $result= "<channel>".
        "<title>".$title.((!empty($category['title']))?' '.$category['title']:'')."</title>".
        "<link>".url::site('reports/'.((!empty($categoty['id']))?'?c='.$category['id']:''))."</link>".
        "<description>".$description.((!empty($category['description']))?' '.$category['description']:'')."</description>";
        foreach ($items as $item){
            $dateadd = ''; 
            if (!empty($item['dateadd'])){
                $date = new DateTime($item['dateadd']);
                $dateadd = $date->format("r");
            }
            
            $result.="<item>
            <title>".$item['title']."</title>
            <link>".url::site("reports/view/".$item['id'])."</link>
            <description>".$item['description']."</description>
            <pubDate>".$dateadd."</pubDate>
            </item>";
        }
        $result.='</channel>';
        
        return $result;
    }
     
	
}