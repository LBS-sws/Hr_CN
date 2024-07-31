<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UploadImgForm extends CFormModel
{
	/* User Fields */
	public $file;

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('file', 'file', 'types'=>'jpg,jpeg,png', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
	}

}
