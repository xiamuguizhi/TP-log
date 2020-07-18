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
use \tpfcore\util\Data;
use \tpfcore\Core;
/**
 *  导航逻辑
 */
class Nav extends AdminBase
{
	private $arr=[];
	public function saveNav($data){
		$validate=\think\Loader::validate($this->name);
		$validate_result = $validate->scene('add')->check($data);
        if (!$validate_result) {    
            return [RESULT_ERROR, $validate->getError(), null];
        }
		$last_id=Core::loadModel($this->name)->saveObject($data);
		if($last_id){
        	return [RESULT_SUCCESS, '操作成功', url('Nav/index')];
        }
	}
	public function delNav($data){
		return self::deleteObject($data,true)?[RESULT_SUCCESS, '删除成功', url('Nav/index')]:[RESULT_ERROR, '删除失败', url('Nav/index')];
	}
	public function getNavListByid($data){
		return self::getOneObject($data);
	}
	public function getNavList($where=[]){
		$result=self::getObject($where,"*","sort ASC");
		$new_result=[];
		foreach ($result as $key => $value) {
			$new_result[$key]=$value->toArray();
		}
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        foreach ($new_result as $n=> $r) {
        	$new_result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . ' collapsed"' : 'class="expanded"';
        	$new_result[$n]['manage'] = '<div class="btn-group"><a class="btn btn-xs btn-success" href="' . url("Nav/edit", ["id" => $r['id']]) . '" title="编辑" data-toggle="tooltip"><i class="mdi mdi-pencil"></i></a><a href="' . url("Nav/del", ["id" => $r['id']]). '" class="btn btn-xs btn-danger js-ajax-delete" title="删除菜单" data-toggle="tooltip" data-msg="您确定删除吗？" ><i class="mdi mdi-window-close"></i></a></div>';
            $new_result[$n]['display'] = $r['display'] ? "<font class='text-success'>显示</font>" : "<font class='text-danger'>隐藏</font>";
        }
       
        $tree->init($new_result);
        $action=url("Nav/ajaxdata");
        $str = "<tr id='node-\$id' \$parentid_node>
					<td><span class='expander'></span><input name='listorders[\$id]' type='text' size='3' value='\$sort' data='Nav|sort|id|\$id' class='form-input input-order ajax-text' action='$action'></td>
					<td>\$id</td>
					<td>\$spacer\$label</td>
					<td>\$href</td>
				    <td>\$display</td>
					<td>\$manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
		return $categorys;
	}
	public function getTreeNav($data=[]){
		$parentid=empty($data['id'])?(empty($data['parentid'])?-1:$data['parentid']):$data['id'];
		if(!empty($data['id'])){
			$list=$this->getNavListByid(['id'=>$data['id']]);
			$parentid=$list->parentid;
		}

		$result=self::getObject([],"*","sort ASC");
		$new_result=[];
		foreach ($result as $key => $value) {
			unset($result[0]);
			$new_result[$value['id']]=$value->toArray();
		}
		
        $tree = new Tree();
        $tree->init($new_result);
        $str = "<option value='\$id' \$selected>\$spacer \$label</option>";
        $categorys = $tree->get_tree(0, $str,$parentid);
		return $categorys;
	}
	public function getNavArrTree($where=[],$filter,$returnarr){
		$result=self::getObject($where,"*","sort ASC");
		foreach ($result as $key => $value) {
			$arr[$value['id']]=$value->toArray();
		}
		if($filter&&\think\Session::get("backend_author_sign")['userid']!=1){
			// 如果要进行权限过滤
			$privs=Core::loadModel("User")->getObject(['id'=>\think\Session::get("backend_author_sign")['userid']],"id,privs")[0]->toArray()['privs'];
			if($privs){
				$privs=explode(",", $privs);
				foreach ($arr as $key => $value) {
					if(!in_array($value['id'], $privs)){
						unset($arr[$key]);
					}
				}
			}else{
				$arr=[];
			}
		}
		return $returnarr?$arr:Data::toTreeArrray($arr);
	}
}