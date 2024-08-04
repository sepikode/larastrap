@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card zoomable">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="module">
    $(document).ready(function(){
     $(".zoomable").click(function(){
         var currentFontSize = parseInt($(this).css("font-size"));
         var targetFontSize;

         if (currentFontSize < 40) {
             targetFontSize = 40; // Zoom in
             $(this).css('background-color', '#ff0000'); // Turn clicked paragraph text red using jQuery
         } else {
             targetFontSize = 16; // Zoom out
             $(this).css('background-color', ''); // Change background color to white when zooming out
         }

         $(this).animate({
             fontSize: targetFontSize + "px"
         }, 1000);
     });
 });

     </script>
@endpush
