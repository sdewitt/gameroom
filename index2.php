<h1 class="main_title">Machine Down</h1>
<div class="entry-content">
	<div class='gform_heading'>
		<h3 class="gform_title">Report Machine Issue 2023</h3>
		<p class='gform_description'>Please enter the ID of the arcade/pinball machine (located on the sticker) having an issue and one of our techs will look into the issue ASAP. THANKS!</p>
	</div>
	<form method='post'   id='machine_down'  action='process_down.php'  >
</div>

<ul id='ul_list' class='ul_list'>
	<li id="li_id"  class="li_id">
    	<label class='gfield_label gform-field-label' for='input_121_1' >Machine ID</label>
    	<div class='id'>
        	<input name='idnum' id='idnum' type='text' class='idnum' />
    	</div>
    </li>
    <li id="id_description"  class="id_description">
    	<label class='gfield_label gform-field-label' for='description' >Issue Description (Optional)</label>
      	<div class='issue_description'>
      		<textarea name='description' id='description' class='textarea_description'  rows='10' cols='50'></textarea>
      	</div>
	</li>      
</ul>
		
</div>
<div class='gform_footer top_label'> <input type='submit' id='submit_button' class='submit_button' value='Submit'   /> 
	</form>
</div>
