<?php 

class NewsController extends BaseController
{
    /**
     * 新闻列表
     * @return multitype:unknown
     */
	public function postNews()
	{
	    $offset = $this->data['offset'] ? $this->data['offset'] : '0';
	    $limit = $this->data['limit'] ? $this->data['limit'] : '20';
	    $news = News::where('IsDisplay','1')->skip($offset)->take($limit)->get();
	    
        return json_encode(array('code'=> '200', 'news'=> $news));
	}
	
	/**
	 * 新闻详情
	 */
	public function postNewsdetail(){
	    $newsId = $this->data['newsId'];
	    $newsDetail = News::where('Id',$newsId)->get();
	    
	    return json_encode(array('code'=> '200', 'newsDetail'=> $newsDetail));
	}

   
}