{**
 * templates/frontend/pages/indexSite.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site index.
 *
 *}
{include file="frontend/components/header.tpl" immersionIndexType="indexSite"}


<!-- bloc-2 -->
<div class="bloc b-divider bgc-white l-bloc" id="bloc-2">
	<div class="container bloc-md">
		<div class="row">
			<div class="col-lg-2">
				<div class="row">
					<div class="col">
						<div class="row">
							<div class="col">
								<div class="row">
									<div class="col-lg-12 col-md-3 col-sm-4 col-7">
										<a href="http://wiki.openarchives.it/index.php/Pagina_principale" target="_blank"><img src="{$baseUrl}/plugins/themes/NUimmersion/templates/images/lazyload-ph.png" data-src="{$baseUrl}/plugins/themes/NUimmersion/templates/images/open-access.png" class="img-fluid mx-auto d-block lazyload" /></a>
									</div>
									<div class="col-lg-12 col-md-3 align-self-center">
										<h4 class="mg-lg">
											<span class="fa fa-question-circle icon-dark-midnight-blue"></span>&nbsp;<a href="index.html">Aiuto e guida</a>
										</h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-7">
				{if $about}
					<div class="about_site">
						{$about|strip_unsafe_html|nl2br}
					</div>
				{/if}
			</div>
			<div class="col-lg-3">
				<h3 class="mg-md tc-dark-midnight-blue">
					<span class="fa fa-gears"></span> Cruscotto
				</h3>
			    {if $hasSidebar}
			    	<div class="sidebar_wrapper row">
			    		{call_hook name="Templates::Common::Sidebar"}
			    	</div>
			    {/if}
			</div>
		</div>
	</div>
</div>
<!-- bloc-2 END -->


<!-- bloc-3 -->
<div class="bloc bgc-isabelline l-bloc" id="bloc-3">
	<div class="container bloc-sm">
		<div class="row">
			<h2 class="title">
				{translate key="journal.journals"}
			</h2>
		</div>
		<div class="row">
			{iterate from=journals item=journal}
				{capture assign="url"}{url journal=$journal->getPath()}{/capture}
				{assign var="thumb" value=$journal->getLocalizedSetting('journalThumbnail')}
				{assign var="description" value=$journal->getLocalizedDescription()}
						<div class="col-md-6">
							<div {if $thumb} class="has_thumb"{/if}>
								<div class="journalHpContainer" itemscope="" itemtype="http://schema.org/Periodical">
									{if $thumb}
										<div class="thumb homepageImage">
											<a href="{$url|escape}">
												<img src="{$journalFilesPath}{$journal->getId()}/{$thumb.uploadName|escape:"url"}"{if $thumb.altText} alt="{$thumb.altText|escape}"{/if}>
											</a>
										</div>
									{/if}
								
									<h3 itemprop="name">
										<a href="{$url|escape}" rel="bookmark">
											{$journal->getLocalizedName()}
										</a>
									</h3>
									{if $description}
										<div class="journalDescription">
											{$description|nl2br}
										</div>
									{/if}
												
									<p><a href="{$url|escape}" class="action">
										{translate key="site.journalView"}
									</a> | 
								
									<a href="{url|escape journal=$journal->getPath() page="issue" op="current"}" class="action">
										{translate key="site.journalCurrent"}
									</a>
									
									</p>
								</div>
								
							</div>
						</div>
			{/iterate}
			
		</div>
	</div>
</div>
<!-- bloc-3 END -->

<!-- ScrollToTop Button -->
<a class="bloc-button btn btn-d scrollToTop" onclick="scrollToTarget('1',this)"><span class="fa fa-chevron-up"></span></a>
<!-- ScrollToTop Button END-->





<main class="container main__content" id="immersion_content_main">
	<div class="row">
		<div class="offset-md-1 col-md-10 offset-lg-2 col-lg-8">
			<header class="main__header">
				
				
			</header>

			<div class="content-body">
				{if !count($journals)}
					{translate key="site.noJournals"}
				{else}
					<ul class="index-site__journals">
						{iterate from=journals item=journal}
							{capture assign="url"}{url journal=$journal->getPath()}{/capture}
							{assign var="thumb" value=$journal->getLocalizedSetting('journalThumbnail')}
							{assign var="description" value=$journal->getLocalizedDescription()}
							<li{if $thumb} class="has_thumb"{/if}>
								{if $thumb}
									<div class="thumb">
										<a class="img-wrapper" href="{$url|escape}">
											<img class="img-thumbnail" src="{$journalFilesPath}{$journal->getId()}/{$thumb.uploadName|escape:"url"}"{if $thumb.altText} alt="{$thumb.altText|escape}"{/if}>
										</a>
									</div>
								{/if}

								<h3>
									<a href="{$url|escape}" rel="bookmark">
										{$journal->getLocalizedName()}
									</a>
								</h3>
								{if $description}
									<div class="description">
										{$description|nl2br}
									</div>
								{/if}
								<div class="index-site__links">
									<a class="btn btn-primary"  href="{$url|escape}">
										{translate key="site.journalView"}
									</a>
									<a class="btn btn-secondary" href="{url|escape journal=$journal->getPath() page="issue" op="current"}">
										{translate key="site.journalCurrent"}
									</a>
								</div>
							</li>
						{/iterate}
					</ul>

					{if $journals->getPageCount() > 0}
						<div class="cmp_pagination">
							{page_info iterator=$journals}
							{page_links anchor="journals" name="journals" iterator=$journals}
						</div>
					{/if}
				{/if}
			</div>
		</div>
	</div><!-- .row -->

</main><!-- .page -->

{include file="frontend/components/footer.tpl"}
