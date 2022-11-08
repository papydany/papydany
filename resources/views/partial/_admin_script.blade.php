<!-- jQuery -->
<script src="{{asset('panel/js/jquery.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{asset('panel/js/bootstrap.min.js')}}"></script>

<script src="{{URL::to('parsley.min.js')}}"></script>


<script type="text/javascript">
   $('#exampleModal').modal('show')
</script>
<script>
     $("#all_ids").change(function(){  //"select all" change 
    var status = this.checked; // "select all" checked status
    $('.ids').each(function(){ //iterate all listed checkbox items
        this.checked = status; //change ".checkbox" checked status
    });
});
</script>

