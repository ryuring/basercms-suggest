<?php
/* SVN FILE: $Id$ */
/**
 * [Suggest] 設定ページ
 *
 * PHP version 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2011 - 2012, Catchup, Inc.
 * @link			http://www.e-catchup.jp Catchup, Inc.
 * @package			suggest.controllers
 * @since			Baser v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT lincense
 */
/**
 * Include files
 */
App::import('Controller', 'Plugins');
/**
 * サジェストコントローラー
 *
 * @package	suggest.controllers
 */
class SuggestConfigsController extends AppController {
/**
 * クラス名
 * 
 * @var string
 * @access public
 */
	public $name = 'SuggestConfigs';
/**
 * モデル
 * 
 * @var array
 * @access public
 */
	public $uses = array('Plugin', 'Suggest.SuggestConfig', 'Suggest.SuggestKeyword');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array('suggest');
/**
 * [ADMIN] サジェスト設定
 *
 * @return	void
 * @access	public
 */
	public function admin_index() {
		
		$this->pageTitle = 'サジェスト設定';
		
		if(!$this->request->data) {
			
			$this->request->data['SuggestConfig'] = $this->SuggestConfig->findExpanded();
			
		} else {
			
			$this->request->data['SuggestConfig']['exclude_keywords'] = mb_convert_kana(strtolower($this->request->data['SuggestConfig']['exclude_keywords']), 'a', Configure::read('App.encoding'));
			$this->SuggestConfig->set($this->request->data);
			
			if($this->SuggestConfig->validates()) {
				
				$this->SuggestConfig->saveKeyValue($this->request->data);
				
				// 除外キーワードを削除
				if($this->request->data['SuggestConfig']['exclude_keywords']) {

					$excludeKeywords = array();
					$excludeKeywords = explode(',', $this->request->data['SuggestConfig']['exclude_keywords']);
					$conditions = array();

					foreach($excludeKeywords as $key => $value) {
						$conditions['or'][$key] = array('SuggestKeyword.name LIKE' => "%{$value}%");
					}

					$suggestKeywords = $this->SuggestKeyword->find('all', array(
						'fields' => array('SuggestKeyword.id'), 
						'conditions' => $conditions
					));

					foreach($suggestKeywords as $suggestKeyword) {
						$this->SuggestKeyword->delete($suggestKeyword['SuggestKeyword']['id']);
					}

				}
				
				$this->setMessage('サジェスト設定を保存しました。');
				$this->redirect('index');
				
			} else {
				
				$this->setMessage('入力エラーです。内容を修正してください。', true);
				
			}
			
		}
		
	}
	
}