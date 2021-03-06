<?php
/**
 *
 *
 * 2014-1-9
 *
 * @author  cyl
 * @version 1.0
 */
class LandscapeThemeModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'landscape_theme';
	public $pk         = 'id';

	//保存景区的
	public function saveLandscapeTheme($landscapeId, $themeIds)
	{
		if($themeIds){
			//先将之前的删除掉，再添加
			$this->del('landscape_id='.$landscapeId, '', '');
			$insertSql = 'INSERT INTO '.$this->table.' (`landscape_id`,`theme_id`)';
			$addItmes  = array();
			foreach($themeIds as $value){
				$addItmes[] = "('{$landscapeId}','{$value}')";
			}
			$insertSql .= 'VALUES '.implode(',', $addItmes);
			$result     = $this->query($insertSql);
			$affectedRows = $this->affectedRows();
			if($result && $affectedRows >= 1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}