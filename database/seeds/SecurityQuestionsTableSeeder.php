<?php

use App\SecurityQuestion;
use Illuminate\Database\Seeder;

class SecurityQuestionsTableSeeder extends Seeder
{
  public function run()
  {
    $questions = [
      'What was the house number and street name you lived in as a child?',
      'What were the last four digits of your childhood telephone number?',
      'What primary school did you attend?',
      'In what town or city was your first full time job?',
      'In what town or city did you meet your spouse/partner?',
      'What is the middle name of your oldest child?',
      'What are the last five digits of your driver\'s licence number?',
      'What is your grandmother\'s (on your mother\'s side) maiden name?',
      'What is your spouse or partner\'s mother\'s maiden name?',
      'In what town or city did your mother and father meet?'
    ];
    foreach ($questions as $question) {
      SecurityQuestion::create(['question' => $question]);
    }
  }
}
