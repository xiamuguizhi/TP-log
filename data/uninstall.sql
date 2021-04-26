DELETE FROM `tpf_hook` WHERE `name` = 'ueditor' and `module` = 'ueditor';
DELETE FROM `tpf_addon` WHERE `module` = 'ueditor' AND `type` ='behavior';