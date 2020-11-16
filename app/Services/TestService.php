<?php


namespace App\Services;


use App\Models\Subject;
use App\Models\TestForm;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TestService
{
    public function create($name) {
        $subject = Subject::where('name', $name)->first();

        TestForm::create(['subject_id' => $subject->id]);
    }
    public function deleteLast() {
        $test_form = TestForm::query()->orderBy('created_at', 'desc')->first();
        if($test_form) {
            $test_form->delete();
        }
    }

    public function update($field, $value) {
        $test_form = TestForm::query()->orderBy('created_at', 'desc')->first();
        if($test_form) {
            $test_form->update([$field => $value]);
        }
        if($field == 'date_stop') {
            $test_form->update(['date_start' => Carbon::now()->format('Y-m-d') ]);
        }
        $test_form->subject;
        Log::info($test_form);
        return $test_form;
    }
}
