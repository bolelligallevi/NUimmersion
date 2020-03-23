{**
 * templates/frontend/components/footer.tpl
 *
 * Copyright (c) 2019 Gianluca Savini
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Common site frontend footer.
 *
 *}
 <!-- bloc-4 -->
 <div class="bloc bgc-gainsboro tc-dark-midnight-blue l-bloc" id="bloc-4">
 	<div class="container bloc-sm">
 		<div class="row">
 			<div class="col-md-4 col-lg-5">
 			
 				<p class="p-style">
 					Università degli Studi di Milano<br />Via Festa del Perdono 7 - 20122 Milano<br />Tel. +39 02503 111<br /><a href="https://www.unimi.it/posta-elettronica-certificata-pec">Posta Elettronica Certificata</a><br />C.F. 80012650158 - P.I. 03064870151<br />© Copyright 2019
 				</p>
 			
 			</div>
 			<div class="col-md-4 offset-lg-0 col-lg-4 align-self-center">
 				<p class="text-lg-center p-bloc-4-style">
 					<a href="https://www.unimi.it/it/amministrazione-trasparente">Amministrazione trasparente</a> &nbsp;| &nbsp;<a href="https://www.unimi.it/it/footer/accessibilita">Accessibilità</a> &nbsp;| &nbsp;<a href="https://www.unimi.it/it/footer/privacy-e-cookie">Privacy e cookie</a> &nbsp;| &nbsp;<a href="https://www.unimi.it/it/footer/note-legali">Note legali</a><br><br>Realizzato con software&nbsp;<a href="http://pkp.sfu.ca/ojs/" target="_blank">OJS</a>&nbsp; curato e mantenuto da&nbsp;<a href="http://www.4science.it/" target="_blank">4Science</a>.<br>Homepage di <a href="http://www.isoladipasqua.it" target="_blank" title="Isoladipasqua di Gianluca Savini">Isoladipasqua</a>.
 				</p>
 			</div>
 			<div class="col-md-4 col-lg-2 offset-lg-1">
 				<img src="{$baseUrl}/plugins/themes/NUimmersion/templates/images/unimi-blu.png" class="img-fluid mx-auto d-block" />
 			</div>
 		</div>
 	</div>
 </div>
 <!-- bloc-4 END -->
 
 </div>
 <!-- Main container END -->



{* Login modal *}
{if $requestedOp|escape != "register"}
	<div id="loginModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					{include file="frontend/components/loginForm.tpl" formType = "loginModal"}
				</div>
			</div>
		</div>
	</div>
{/if}

{load_script context="frontend"}

{call_hook name="Templates::Common::Footer::PageFooter"}



</body>
</html>
