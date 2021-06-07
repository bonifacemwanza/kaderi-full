<?php

if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}

if (!isset($_SESSION['score'])){
   $_SESSION['score'] = 0;
   $_SESSION['question']=array();
   $_SESSION['answer']=array();
}
if($first == 'check'){
    $QuestionFull = '';
    $ChoiceFull = '';
    $message_li = '';
    $icon = false;
    $lesson_title = Secure($_POST['lesson_title']);
    $quiz_number = Secure($_POST['quiz_number']);
    $selected_choice = Secure($_POST['choice']);
    $next = $quiz_number + 1;
    $lesson_number = Secure($_POST['lesson_number']);
    $book_number = Secure($_POST['book_number']);


    $get_question = $db->where('lesson_number',$lesson_number)->where('book_number', $book_number)->where('language',$_SESSION['lang'])->getOne(T_QUIZ,array('count(*) as total'));
    $q_total = $get_question->total;
     $get_question_s = $db->where('lesson_number',$lesson_number)->where('book_number', $book_number)->where('language',$_SESSION['lang'])->getOne(T_QUIZ);
     $QuestionFull = htmlspecialchars_decode($get_question_s->question);
 

    $get_choice = $db->where('lesson_number',$lesson_number)->where('quiz_number',$quiz_number)->where('book_number', $book_number)->where('is_correct','1')->where('language',$_SESSION['lang'])->getOne(T_CHOICE);
    $correct_choice = $get_choice->id;
    $ChoiceFull = $get_choice->choice;
     


    if($correct_choice == $selected_choice){
		$_SESSION['score']++;
		$message_li =  __('correct_answer');
		$icon = true;

	} else{
		$_SESSION['question'][$next-1] = $QuestionFull;
		$_SESSION['answer'][$next-1] = $ChoiceFull;
		$message_li = '<b>'.__('wrong_answer') .'</b> '. $ChoiceFull;

	}

  

    $quiz_review = '';
	$progress_percentage = '';
	$progress_percentage = $quiz_number / $q_total * 100;
	$rounded = round($progress_percentage, 2);
	
     $quiz_data_exist = $db->where('user_id', $kd->user->id)->where('book_number', $book_number)->where('lesson_number',$lesson_number)->getValue(T_QUIZ_DATA, "count(*)");
	
	 if($quiz_data_exist > 0){
		$data = array(
			
			"last_question_number" => intval($quiz_number),
			"status" => ''.$rounded .'%',
			"score" => intval($_SESSION['score'])
		);
		$updata_quiz_status = $db->where('user_id', $kd->user->id)->where('book_number', $book_number)->where('lesson_number',$lesson_number)->update(T_QUIZ_DATA, $data);
		// var_dump($data);
		// var_dump($updata_quiz_status);
	 } else {
		 $data = array(
             "user_id" => $kd->user->id,
			 "book_number" => intval($book_number),
			 "lesson_number" => intval($lesson_number),
			 "last_question_number" => $quiz_number,
			 "score" => intval($_SESSION['score'])
		 );
		$quiz_data_exists = $db->insert(T_QUIZ_DATA, $data);
	 }

	
	if($quiz_number == $q_total){
// var_dump($_SESSION['question']);
		for ($i = 0; $i <= count($_SESSION['question']); ++$i) {
			if($_SESSION['question'][ $i ] != NULL){
				$quiz_review .=  '<span class="failed-Q-item">'.$i.' '.$_SESSION['question'][ $i ].' </span><span class="failed-A-item">'.$_SESSION['answer'][ $i ].'</span>';	
			}
		}
		$save_point = $db->where('id', $kd->user->id)->update(T_USERS,array('points'=>$db->inc(Secure($_SESSION['score']))));

	    if($save_point){
			$data = array(
	          'status' => 200,
	          'icon' => $icon,
	          'mode' => 'completed',
	          'message' => __('quiz_completed'),
	          'progress' => $progress_percentage,
	          'html' => '<h2>'. __('quiz_completed') .'</h2>',
	          'final' => LoadPage('courses/quiz_final', array('REVIEW_HTML' => $quiz_review,'FINAL_POINT' =>  $_SESSION['score'] )),
			);
			unset($_SESSION['answer']);
	        unset($_SESSION['question']);
		    unset($_SESSION['score']);
		}
		

	} else {

		 $getQuiz = '';
		 $choice_html = '';
		 $quiz_html = '';


		 $getQuiz = $db->rawQuery('SELECT * FROM `'.T_QUIZ.'` WHERE lesson_number = '.$lesson_number.' AND book_number = '.$book_number.' AND language = "'.$_SESSION['lang'].'" AND quiz_number = "'.$next.'"');
		 $getChoices = $db->rawQuery('SELECT * FROM `'.T_CHOICE.'` WHERE lesson_number = '.$lesson_number.' AND book_number = '.$book_number.' AND language = "'.$_SESSION['lang'].'" AND quiz_number = "'.$next.'"');

		if(!empty($getChoices)){
		     foreach ($getChoices as $key => $ck) {

		  $choice_html .= '<div class="quiz-answer-option">
					            <label>
					                <input name="choice" id="optionsRadios2" value="'.$ck->id.'" type="radio">'.$ck->choice.'
					            </label>
					       </div>'; 
		    }
		}
		if(!empty($getQuiz)){
		     foreach ($getQuiz as $key => $gq) {
		         $kd->gq = $gq;

		     }
		}
 

		$data = array( 
	
	     'status' => 200,   
	     'mode' => 'next_question',
         'message' => $message_li,
         'total' => $q_total,
         'icon' => $icon,
         'lesson_title' =>$lesson_title,
         'lesson_number' =>$kd->gq->lesson_number,
         'quiz_number' => $kd->gq->quiz_number,
         'book_number' => $kd->gq->book_number,
         'progress' => $progress_percentage,
         'quiz_question' =>htmlspecialchars_decode($kd->gq->question),
         'choices' => $choice_html 
       
	    );
	}



}
header("Content-type: application/json");
if (isset($errors)) {
    echo json_encode(array(
        'errors' => $errors
    ));
    exit();
}
