
@foreach ($csvFiles as $key => $file)
    <?php 
    // echo $file;
    // echo '<br>';

    //echo $file;exit;
    ?>
    <p><a href="{{ url('/csv/download/'.$key) }}" class=""><button class=""> {{$file}} </button></a></p>
               
@endforeach
