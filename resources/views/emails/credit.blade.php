@component('mail::message')
# @lang('License Purchase Detail')
 
@foreach($credits as $credit)  
## @lang('credit')  #{{$loop->index+1}}
<hr>  
@foreach(collect($credit->data) as $key => $value) 
### {{ $key }}: {{ $value }} 
@endforeach   
<br>
@endforeach 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
