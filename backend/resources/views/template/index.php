<?php
use  App\imageTemp;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\DocumentTemplate;
?>
<h1>working fine</h1>

<?= $test = imageTemp::first()->id?>
<br>
<a href="<?=route('download',3)?>">Some Text for download</a>
<?=  Storage::download('images/evqQgE9eunan0eSS2BJHlMJKnS4WmJMtswVIUT5u.jpeg')?>



