<?php
use App\Models\Prize;
$currentProbability = floatval(Prize::sum('probability'));
$remainingProbability = 100 - $currentProbability;
?>

<h3 class="text-center mb-3">
    <u>{{ __('message.mainHeader') }}</u>
</h3>

<!-- display error message in alert box -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ __('message.error1') }} {{ __('message.error2') }} <bold> {{ $currentProbability }}% </bold>
    {{ __('message.error3') }} <bold>{{ $remainingProbability }}% </bold> {{ __('message.error4') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
