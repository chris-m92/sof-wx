<!DOCTYPE html>
<html lang="en">
	<head><?php require("../req/head.php"); ?></head>
	<body>
		<?php // Navigation / Header ?>
		<?php require("../req/navbar.php"); ?>
				
		<?php // Body Container ?>
		<div id="body-container" class="container d-none" style="margin-top: 2rem; margin-bottom: 2rem;">
		
			<?php // Spinning Loader ?>
			<?php require("../req/loader.php"); ?>
				
			<?php // Alert Container ?>
			<div id="alert" class="alert" role="alert"></div>
			
			<div class="card">
				<div class="card-header">
					<h4>SOF WX Privacy Policy</h4>
				</div>
				<div class="card-body">
					<h5>Last Updated: 5 December 2018</h5>
					<p>Please read these Terms of Use ("Terms") carefully before using http://sof-wx.com (the "Site", or "Service") operated by SOF WX("us", "we", or "our").</p>
					<p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users, and others who access or use the Service.</p>
					<p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any partof the terms, then you may not access or otherwise use the Service.</p>
					<h4>Terms</h4>
					<h5>You May:</h5>
					<ol type="1">
						<li>Use the Service to its full extent.</li>
						<li>Use the Service as often or as little as you like, for an unlimited amount of time.</li>
					</ol>
					<h5>You May Not:</h5>
					<ol type="1">
						<li>Attempt to gain access to the Service (It's not like we store anything, there's nothing to access)</li>
						<li>Attempt to harm the Site or impede its ability to provide the Service. Including, but not limited to, DDOS, MITM, XSS, or any other attacks which could harm the Site or anybody using the Service.</li>
					</ol>
					<h4>Termination</h4>
					<p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
					<p>All provisions of the Terms which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, and limitations of liability.</p>
				</div>
			</div>
		</div>
		<?php // Footer ?>
		<?php require("../req/footer.php"); ?>
	</body>
</html>