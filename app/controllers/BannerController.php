<?php 

class BannerController extends BaseController
{
    /**
     * 横幅列表
     * @return multitype:unknown
     */
	public function postBannerlist()
	{
	    $limit = $this->data['limit'] ? $this->data['limit'] : '20';
	   
	    $bannerList = Banner::orderBy('Sort','desc')->take($limit)->get();
	    
	    return json_encode(array('code'=> '200', 'bannerList'=> $bannerList));
	}


   
}