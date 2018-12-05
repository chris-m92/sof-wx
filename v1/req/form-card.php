<div class="card" id="form-card">
	<div class="card-header text-center">
		<h2>SOF Planner</h2>
	</div>
	
	<form>
		<div class="card-body">
			<p class="card-text">Insert ICAOs, separated by a space</p>
			<div class="form-group">
				<label for="icao">ICAOs</label>
				<input type="text" class="form-control" id="icao" name="icao">
				<div class="form-control-feedback" id="icao-fb"></div>
			</div>
		</div>
		<div class="card-footer text-center">
			<button id="submit-button" class="btn btn-block btn-primary" type="submit">View ICAO data</button>
			<small><?php require("../req/cookie-text.php"); ?>  </small>
		</div>
	</form>
</div>