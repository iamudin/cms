@if(get_post_type()=='media')
@include('views::backend.form-media')
@elseif(get_post_type()=='menu')
@include('views::backend.form-menu')

@else
@include('views::backend.form-default')
@endif

