<?php
/**
 * ============================================================================
 * 版权所有 2017-2077 tpframe工作室，并保留所有权利。
 * @link http://www.tpframe.com/
 * @copyright Copyright (c) 2017 TPFrame Software LLC
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace app\backend\logic;
use \tpfcore\util\Tree;
use tpfcore\Core;
use tpfcore\helpers\StringHelper;
use think\Db;
use \think\Config;

class Sort extends AdminBase
{
	public $ids=[];
	public function getChildIds($parentid){
		$list=self::getObject(['parentid'=>$parentid]);
		foreach ($list as $key => $value) {
			$this->ids[]=$value['id'];
			$this->getChildIds($value['id']);
		}
		return $this->ids;
	}
	
	public function getSort($data=[]){
		return self::getList($data);
	}
	
	
	#保存
	public function saveCategory($data){
		$validate=\think\Loader::validate($this->name);
        $validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url("sort/index")];
        }else{
        	return [RESULT_ERROR, '操作失败', null];
        }
	}
	
	
	#删除	
	public function delCategory($data){
		$where = ['cateid'=>$data['id']];	
		$listPosts=Core::loadModel("Blog")->where($where)->find();			
		if($listPosts){
			return [RESULT_ERROR , '删除失败，该分类下还有文章', url("sort/index")];
		}		
		$childs=self::getList(["where"=>['parentid'=>$data['id']]]);		
		if(count($childs)>0){
			return [RESULT_ERROR , '删除失败，该分类下还有子分类', url("sort/index")];
		}
		return self::deleteObject(["id"=>$data['id']],true)?[RESULT_SUCCESS, '删除成功', url("sort/index")]:[RESULT_ERROR, '删除失败', url("sort/index")];
	}
	
	
	
	public function getCategoryListByid($data){
		return self::getOneObject($data);
	}
	
	
	#分类列表
	public function getCategoryList($data){
		$result=self::getObject([],"*","sort ASC");
		$new_result=[];
		foreach ($result as $key => $value) {
			$new_result[$key]=$value->toArray();
		}		
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($new_result as $n=> $r) {
        	$new_result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . ' collapsed"' : 'class="expanded"';
            $new_result[$n]['manage'] = '<div class="btn-group"><a class="btn btn-xs btn-primary" href="' . url("sort/add", ["parentid" => $r['id']]) . '" title="添加分类" data-toggle="tooltip"><i class="mdi mdi-plus"></i></a><a class="btn btn-xs btn-success" href="' . url("sort/edit", ["id" => $r['id']]) . '" title="编辑" data-toggle="tooltip"><i class="mdi mdi-pencil"></i></a><a href="' . url("sort/del", ["id" => $r['id']]). '" class="btn btn-xs btn-danger js-ajax-delete" title="删除" data-toggle="tooltip" data-msg="您确定删除吗？"><i class="mdi mdi-window-close"></i></a></div>';
            $new_result[$n]['display'] = $r['display'] ? "<font class='text-success'>显示</font>" : "<font class='text-danger'>隐藏</font>";
        }
       	
       	$url=url('sort/ajaxdata');
        $tree->init($new_result);
        $str = "<tr id='node-\$id' \$parentid_node>
					<td>\$id</td>
					<td>\$spacer\$title</td>
					<td style='padding-left:20px;'><input name='listorders[\$id]' data='sort|sort|id|\$id' type='text' size='3' value='\$sort' action='$url' class='form-input input-order ajax-text'></td>
				    <td>\$display</td>
					<td>\$manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
		return $categorys;
	}
	
	#分类
	public function getTreeCategory($data=[]){
		$parentid=empty($data['id'])?(empty($data['parentid'])?-1:$data['parentid']):$data['id'];
		if(!empty($data['id'])){
			$list=$this->getCategoryListByid(['id'=>$data['id']]);
			$parentid=$list->parentid;
		}
		if(!empty($data['cid'])){
			$parentid=$data['cid'];
		}
		if(!empty($data['parentid'])){
			$parentid=$data['parentid'];
		}
		$result=self::getObject([],"*","sort ASC");
		$new_result=[];
		foreach ($result as $key => $value) {
			unset($result[0]);
			$new_result[$value['id']]=$value->toArray();
		}		
        $tree = new Tree();
        $tree->init($new_result);
        $str = "<option value='\$id' \$selected>\$spacer \$title</option>";
        $categorys = $tree->get_tree(0, $str,$parentid);
		return $categorys;
	}
	
		public function getTreeNav($data=[]){
		$parentid=empty($data['id'])?(empty($data['parentid'])?-1:$data['parentid']):$data['id'];
		if(!empty($data['cateid'])){
			$parentid=$data['cateid'];
		}
		$result=self::getObject([],"*","sort ASC");
		$arr=[];
		foreach ($result as $key => $value) {
			$arr[$value['id']]=$value->toArray();
		}
        $tree = new Tree();
        $tree->init($arr);
        $str = "<option value='/sort/\$id' data-name='\$title' \$selected>\$spacer \$title</option>";
        $categorys = $tree->get_tree(0, $str,$parentid);
		return $categorys;
	}
}