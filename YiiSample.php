<?php
class GamesController extends Controller
{
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	/* 
		*Show games main category *
		@return html page 
	*/
	public function actionIndex()
	{

		$user_name				= Yii::app()->user->hasState("user_name");
		$guest_grade			= Yii::app()->user->hasState("guest_grade");
		$levelId				= '';
		$this->layout			= 'homepage';
		Yii::app()->user->setFlash('snow',true);
		$breadcrumb				= Yii::t('zii','Game');
		$link					= yii::app()->request->baseUrl;
		$model					= new Game();
		$Bigmenu				= Bigmenu::model()->findByPk(18);
		$this->pageTitle		= Yii::t('zii','Game');
		$innerSnd				= true;
		if($user_name){
			$time				= time();
			Yii::app()->user->setState("logout_time",$time+(60*90));
			$gGradeId			= $this->getUserInfo()->student_grade_id;	
		// Start non native data	
			$native						= $this->getUserInfo()->native;
			if($native=='no' && !empty($this->getUserInfo()->level_id))
				$levelId				= $this->getUserInfo()->level_id;
			if(!empty($levelId)){
				$condition				= 'status="active" and find_in_set('.$levelId.',show_in_level) <> 0  and parent_id IS NULL  order BY sort_order =0 ASC,sort_order';
				$result					= $model->findAll(array('condition'=>$condition));
				$this->render('game',array('Bigmenu'=>$Bigmenu,'result'=>$result,'breadcrumb'=>$breadcrumb,'link'=>$link,'innerSnd'=>$innerSnd,'levelId'=>$levelId));
				Yii::app()->end();
			}
		// End non native datas	
		}
		elseif($guest_grade){
			$gGradeId		 	= Yii::app()->user->getState("guest_grade");
			
		}else	
			$this->logincheck();
		
		$condition				= 'status="active" and find_in_set('.$gGradeId.',grade_id) <> 0  and parent_id IS NULL and hide_from_main_site="no" order BY sort_order =0 ASC,sort_order';
		$result					= $model->findAll(array('condition'=>$condition));
		$this->render('game',array('Bigmenu'=>$Bigmenu,'result'=>$result,'breadcrumb'=>$breadcrumb,'link'=>$link,'innerSnd'=>$innerSnd,'levelId'=>$levelId));
	}
	public function actionGameInfo()
	{
		
		$levelId				= '';
		$this->layout			= 'homepage';
		$user_name				= Yii::app()->user->hasState("user_name");
		$guest_grade			= Yii::app()->user->hasState("guest_grade");
		$slug					= Yii::app()->request->getQuery('slug');
		$game					= new Game();
		if($user_name){
			$time				= time();
			Yii::app()->user->setState("logout_time",$time+(60*90));
			$gGradeId			= $this->getUserInfo()->student_grade_id;
		// Start non native data	
			$native						= $this->getUserInfo()->native;
			if($native=='no' && !empty($this->getUserInfo()->level_id))
				$levelId				= $this->getUserInfo()->level_id;
			if(!empty($levelId)){
				$result				= $game->findByAttributes(array('status'=>'active','slug'=>$slug),'find_in_set('.$levelId.',show_in_level) <> 0');
				$results1			= $game->findByAttributes(array('status'=>'active','slug'=>$slug));
				
				if(!empty($result)){
					$breadcrumb=''; 
					$breadcrumb		= '<a href="'.yii::app()->request->baseUrl.'/games">'.Yii::t('zii','Game').'</a>';
					$link			= 	yii::app()->request->baseUrl.'/games';
					if($result->parent_id!=null)
					{
						$SubEvent		= $result->findByPk($result->parent_id);
						$breadcrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug.'">'.$SubEvent->title.'</a>';	
						$link			= 	yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug;
					}
					$breadcrumb		.= ' > '.$result->title;
				
					$games			= $this->getAllLevelgames($result->game_id,$levelId);
					if(!empty($games)){
					
						Yii::app()->user->setFlash('snow',true);
						$this->pageTitle		= $result->title;
						$innerSnd				= false;
						$this->render('game',array('result'=>$games,'breadcrumb'=>$breadcrumb,'link'=>$link,'game'=>$result,'innerSnd'=>$innerSnd));
						Yii::app()->end();
					} 
					else{
						
						if($result->free_to_web=='no' && (empty($guest_grade) || empty($user_name)))
							$this->logincheck();
						$imagesmodel 		= new Animaltype;
						$condition			= 'game_id = '.$result->game_id.' and  find_in_set('.$levelId.',show_in_level) <> 0';	
						$imagesData			= $imagesmodel->findAll(array('condition'=>$condition));
					 	$totalEngines		= $this->countAllLevelEnginesData($result->game_id,$levelId);
						$this->pageTitle	= $result->title;
						$this->render('gamecontents',array('totalEngines'=>$totalEngines,'breadcrumb'=>$breadcrumb,'imagesData'=>$imagesData,'link'=>$link,'result'=>$result,'slug'=>$slug));
						Yii::app()->end();
						
						
					}
				}
				elseif(!empty($results1)){
					$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','You don\'t have permission to view this page')));
					Yii::app()->end();
				
				}
				else {
					$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','The requested page not found')));
					Yii::app()->end();
				}
				
			}
		// end non native data		
		}
		elseif($guest_grade){
			$gGradeId		 	= Yii::app()->user->getState("guest_grade");
			
		}else	
			$this->logincheck();
		
		
		$result				= $game->findByAttributes(array('status'=>'active','slug'=>$slug,'hide_from_main_site'=>'no'),'find_in_set('.$gGradeId.',grade_id) <> 0');
		$results1			= $game->findByAttributes(array('status'=>'active','slug'=>$slug,'hide_from_main_site'=>'no'));
		if(!empty($result)){
			$breadcrumb=''; 
			$breadcrumb		= '<a href="'.yii::app()->request->baseUrl.'/games">'.Yii::t('zii','Game').'</a>';
			$link			= 	yii::app()->request->baseUrl.'/games';
			if($result->parent_id!=null)
			{
				$SubEvent		= $result->findByPk($result->parent_id);
				$breadcrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug.'">'.$SubEvent->title.'</a>';	
				$link			= 	yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug;
			}
			$breadcrumb		.= ' > '.$result->title;
		
			$games			= $this->getAllgames($result->game_id);
			if(!empty($games)){

				Yii::app()->user->setFlash('snow',true);
				$this->pageTitle		= $result->title;
				$innerSnd				= false;
				$this->render('game',array('result'=>$games,'breadcrumb'=>$breadcrumb,'link'=>$link,'game'=>$result,'innerSnd'=>$innerSnd));
			} 
			else{
				
				if($result->free_to_web=='no' && (empty($guest_grade) || empty($user_name)))
					$this->logincheck();
				$imagesmodel 		= new Animaltype;
				$condition			= 'game_id = '.$result->game_id.' and hide_from_main_site="no"';	
				$imagesData			= $imagesmodel->findAll(array('condition'=>$condition));
				$totalEngines		= $this->countAllEnginesData($result->game_id);
				
				$this->pageTitle	= $result->title;
				$this->render('gamecontents',array('totalEngines'=>$totalEngines,'breadcrumb'=>$breadcrumb,'imagesData'=>$imagesData,'link'=>$link,'result'=>$result,'slug'=>$slug));
			}
		}
		elseif(!empty($results1)){
			$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','You don\'t have permission to view this page')));
		
		}
		else {
			$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','The requested page not found')));
		}
	}
	
	public function actionGamesdataa()
	{
			
		$this->logincheck();
		$this->layout		= 'homepage';		
		$gameDataModel		= new GameData();
		$id					= 	Yii::app()->request->getQuery('id');
		$condition			= 'game_id = '.$id.'';	
		$gameDataRes		= $gameDataModel->findBYAttributes(array(),array('condition'=>$condition)); 
		if(!empty($gameDataRes)){
			$gameCat		= Game::model()->findByPk($id);	
			$link			= yii::app()->request->baseUrl.'/games/'.$gameCat->slug;
			// breadcrumb
				$bigmenudata	= Bigmenu::model()->findByPk(18);
				$breadCrumb		= $gameDataRes->game_name;
				$breadCrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games/'.$gameCat->slug.'">'.$gameCat->title.'</a>';
				$breadCrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games">'.$bigmenudata->bigmenu_title.'</a>';
			// end breadcrumb
			$this->render('gamesdataa',array('link'=>$link,'gameDataRes'=>$gameDataRes,'breadcrumb'=>$breadCrumb));
		}
	}
	public function actionColor()
	{
		$this->logincheck();
		$this->layout		= 'homepage';
	 	$slug		= Yii::app()->request->getQuery('slug');	
		$model 			= new Category;
		$result			= $model->findByAttributes(array('category_slug'=>$slug));
		if(!empty($result)){
			$mainSubCat		= Category::model()->findByPk($result->parent_id);
			$Bigmenu		= Bigmenu::model()->findByPk($result->bigmenu_id);
			$breadcrumb		= '<a href="'.yii::app()->request->baseUrl.'/category/'.$Bigmenu->bigmenu_link.'">'.$result->category_title.'</a>';
			$link			= yii::app()->request->baseUrl.'/category/'.$Bigmenu->bigmenu_link;

			$result->category_slug			= 'color';
			$this->render('index',array('slug'=>$slug,'result'=>$result,'breadcrumb'=>$breadcrumb,'link'=>$link));
		}
	}
	/**
       * 
       * Count all engines data including HTML5 games
	   * @param  $gameId Game id
       * @return numeric
       */
	public function countAllEnginesData($gameId){
	// count all HTML5 games
		$iframeGame			= GameData::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
	// count all Engine1(matching) data	
		$engine1			= Engine1::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
	// count all Engine2(Multiple Choice) data		
		$engine2			= Engine2::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
	// count all Engine3(Drag Drop) data			
		$engine3			= Engine3::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
	// count all Engine4(Color Matching) data		
		$Engine4			= Engine4::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
	// count all Engine5(Color Matching) data		
		$Engine5			= Engine5::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId,'hide_from_main_site'=>'no'));
		return $engine1+$engine2+$engine3+$Engine4+$Engine5+$iframeGame;
	}
	public function countAllLevelEnginesData($gameId,$levelId){
	// count all HTML5 games
		$iframeGame			= GameData::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
	// count all Engine1(matching) data	
		$engine1			= Engine1::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
	// count all Engine2(Multiple Choice) data		
		$engine2			= Engine2::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
	// count all Engine3(Drag Drop) data			
		$engine3			= Engine3::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
	// count all Engine4(Color Matching) data		
		$Engine4			= Engine4::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
	// count all Engine5(Color Matching) data		
		$Engine5			= Engine5::model()->countByAttributes(array('status'=>'active','game_id'=>$gameId),'find_in_set('.$levelId.',show_in_level) <> 0');
		return $engine1+$engine2+$engine3+$Engine4+$Engine5+$iframeGame;
	}
	public function actionAllenginesdatas(){
			$this->logincheck();
			$this->layout		= 'homepage';
			$slug				= Yii::app()->request->getQuery('slug');
			$game				= new Game();
			$result				= $game->findByAttributes(array('status'=>'active','slug'=>$slug),'find_in_set('.$this->getUserInfo()->student_grade_id.',grade_id) <> 0');
			$results1			= $game->findByAttributes(array('status'=>'active','slug'=>$slug));
			$breadcrumb=''; 
			$breadcrumb		= '<a href="'.yii::app()->request->baseUrl.'/games">'.Yii::t('zii','Game').'</a>';
			$link			= 	yii::app()->request->baseUrl.'/games';
		
			if(!empty($result)):
				if($result->parent_id!=null)
				{
					$SubEvent		= $result->findByPk($result->parent_id);
					$breadcrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug.'">'.$SubEvent->title.'</a>';	
					$link			= 	yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug;
				}
				$breadcrumb		.= ' > '.$result->title;
				
				$condition			= 'status="active" and game_id='.$result->game_id;
				$engine1			= Engine1::model()->findAll(array('condition'=>$condition));
				$games				= array();
				if(!empty($engine1)){
					foreach($engine1 as $eng1Data){
						$games[]	= $eng1Data->engine1_id.'&engine=1';
					}
				}
				$gameDataModel		= new GameData();
				$iframeGames		= $gameDataModel->findAll(array('condition'=>$condition));
				if(!empty($iframeGames)){
					foreach($iframeGames as $iframeGame){
						$games[]	= $iframeGame->gamedata_id.'&engine=0';
					}
				}
				$engine2			= Engine2::model()->findAll(array('condition'=>$condition));
				if(!empty($engine2)){
					foreach($engine2 as $eng2Data){
						$games[]	= $eng2Data->engine2_id.'&engine=2';
					}
				}
				$engine3			= Engine3::model()->findAll(array('condition'=>$condition));
				if(!empty($engine3)){
					foreach($engine3 as $eng3Data){
						$games[]	= $eng3Data->engine3_id.'&engine=3';
					}
				}
				$engine4			= Engine4::model()->findAll(array('condition'=>$condition));
				if(!empty($engine4)){
					foreach($engine4 as $eng4Data){
						$games[]	= $eng4Data->engine4_id.'&engine=4';
					}
				}
				$TotalGames			= $this->countAllEnginesData($result->game_id);
				$imagesmodel 		= new Animaltype;
				$condition			= 'game_id = '.$result->game_id.'';	
				$imagesData			= $imagesmodel->findAll(array('condition'=>$condition));
				$this->render('allenginedata',array('breadcrumb'=>$breadcrumb,'link'=>$link,'TotalGames'=>$TotalGames,'games'=>json_encode($games),'slug'=>$slug,'imagesData'=>$imagesData));
				elseif(!empty($results1)):
					$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','You don\'t have permission to view this page')));
				else:
				$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','The requested page not found')));
			endif;	

	}
	/**
       * 
       * Show All engines result including HTML5 games
       * @return string
       */
	public function actionAllenginesdata(){
		$this->logincheck();
		$levelId			= '';
		$native				= $this->getUserInfo()->native;
		if($native=='no' && !empty($this->getUserInfo()->level_id))
			$levelId		= $this->getUserInfo()->level_id;
		$this->layout		= 'homepage';
		Yii::app()->user->setFlash('snow',null);
		$slug				= Yii::app()->request->getQuery('slug');
		$game				= new Game();
		$result				= $game->findByAttributes(array('status'=>'active','slug'=>$slug),' find_in_set('.$this->getUserInfo()->student_grade_id.',grade_id) <> 0 and hide_from_main_site="no"');
	// start level data
		if(!empty($levelId)){
			$result				= $game->findByAttributes(array('status'=>'active','slug'=>$slug),' find_in_set('.$levelId.',show_in_level) <> 0');
		}
	// end level data
		$Permission				= $game->findByAttributes(array('status'=>'active','slug'=>$slug));
		$breadcrumb			=''; 
		$breadcrumb			= '<a href="'.yii::app()->request->baseUrl.'/games">'.Yii::t('zii','Game').'</a>';
		$link				= 	yii::app()->request->baseUrl.'/games';
		if(!empty($result)):
			if($result->parent_id!=null)
			{
				$SubEvent		= $result->findByPk($result->parent_id);
				$breadcrumb		.= ' > <a href="'.yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug.'">'.$SubEvent->title.'</a>';	
				$link			= 	yii::app()->request->baseUrl.'/games/gameinfo/'.$SubEvent->slug;
			}
			$breadcrumb		.= ' > '.$result->title;
		
			$condition			= 'hide_from_main_site="no" and status="active" and game_id='.$result->game_id;
		// start level data
			if(!empty($levelId)){
				$condition			= 'find_in_set('.$levelId.',show_in_level) <> 0 and status="active" and game_id='.$result->game_id;
			}
		// end level data	
			
			$games				= array();
		// get all engine1(matching) data
			$engine1			= Engine1::model()->findAll(array('condition'=>$condition));
			if(!empty($engine1)){
				foreach($engine1 as $eng1Data){
					$games[]	= $eng1Data->engine1_id.'&engine=1';
				}
			}
		// get all engine2(Multiple choice) data
			$engine2			= Engine2::model()->findAll(array('condition'=>$condition));
			if(!empty($engine2)){
				foreach($engine2 as $eng2Data){
					$games[]	= $eng2Data->engine2_id.'&engine=2';
				}
			}

		// get all engine3(Drag Drop) data
			$engine3			= Engine3::model()->findAll(array('condition'=>$condition));
			if(!empty($engine3)){
				foreach($engine3 as $eng3Data){
					$games[]	= $eng3Data->engine3_id.'&engine=3';
				}
			}
		// get all engine4(Color matching) data
			$engine4			= Engine4::model()->findAll(array('condition'=>$condition));
			if(!empty($engine4)){
				foreach($engine4 as $eng4Data){
					$games[]	= $eng4Data->engine4_id.'&engine=4';
				}
			}
		// get all engine5(Image Drop down) data
			$engine5			= Engine5::model()->findAll(array('condition'=>$condition));
			if(!empty($engine5)){
				foreach($engine5 as $eng5Data){
					$games[]	= $eng5Data->engine5_id.'&engine=5';
				}
			}
		// get all HTML5 games data
			$gameDataModel		= new GameData();
			$iframeGames		= $gameDataModel->findAll(array('condition'=>$condition));
			if(!empty($iframeGames)){
				foreach($iframeGames as $iframeGame){
					$games[]	= $iframeGame->gamedata_id.'&engine=0';
				}
			}
		// count all engines and html5 games data
				$TotalGames			= $this->countAllEnginesData($result->game_id);
		// start level data
			if(!empty($levelId)){
				$TotalGames			= $this->countAllLevelEnginesData($result->game_id,$levelId);
			}
		// end level data		
		// get games instructions
			$imagesmodel 		= new Animaltype;
			$condition			= 'hide_from_main_site="no" and game_id = '.$result->game_id.'';
		// start level data
			if(!empty($levelId)){
				$condition			= 'find_in_set('.$levelId.',show_in_level) <> 0 and game_id = '.$result->game_id.'';
			}
		// end level data	
			$imagesData			= $imagesmodel->findAll(array('condition'=>$condition));
			
			$this->render('allenginedata1',array('breadcrumb'=>$breadcrumb,'link'=>$link,'TotalGames'=>$TotalGames,'games'=>json_encode($games),'slug'=>$slug,'imagesData'=>$imagesData));
			
		elseif(!empty($Permission)):
			$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','You don\'t have permission to view this page')));
		else:
			$this->render('//site/error',array('code'=>Yii::t('zii','Page Not Found'),'message'=>Yii::t('zii','The requested page not found')));	
		endif;
	}
}
