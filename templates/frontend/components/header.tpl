{**
 * templates/frontend/components/header.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Site-wide header; includes journal logo, user menu, and primary menu
 * @uses $languageToggleLocales array All supported locales (from the Immersion theme)
 *}

{strip}
	{* Determine whether a logo or title string is being displayed *}
	{assign var="showingLogo" value=true}
	{if $displayPageHeaderTitle && !$displayPageHeaderLogo && is_string($displayPageHeaderTitle)}
		{assign var="showingLogo" value=false}
	{/if}
	{assign var="localeShow" value=false}
	{if $languageToggleLocales && $languageToggleLocales|@count > 1}
		{assign var="localeShow" value=true}
	{/if}
{/strip}

<!DOCTYPE html>

<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{if !$pageTitleTranslated}{capture assign="pageTitleTranslated"}{translate key=$pageTitle}{/capture}{/if}


<head>
	<meta charset="{$defaultCharset|escape}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		{$pageTitleTranslated|strip_tags}
		{* Add the journal name to the end of page titles *}
		{if $requestedPage|escape|default:"index" != 'index' && $currentContext && $currentContext->getLocalizedName()}
			| {$currentContext->getLocalizedName()}
		{/if}
	</title>

	{load_header context="frontend"}
	{load_stylesheet context="frontend"}
</head>



<body class="page_{$requestedPage|escape|default:"index"} op_{$requestedOp|escape|default:"index"}{if $showingLogo} has_site_logo{/if}{if $immersionIndexType} {$immersionIndexType|escape}{/if}"
      dir="{$currentLocaleLangDir|escape|default:"ltr"}">

<div class="cmp_skip_to_content">
	<a class="sr-only" href="#immersion_content_header">{translate key="navigation.skip.nav"}</a>
	<a class="sr-only" href="#immersion_content_main">{translate key="navigation.skip.main"}</a>
	<a class="sr-only" href="#immersion_content_footer">{translate key="navigation.skip.footer"}</a>
</div>

<!-- Main container -->
<div class="page-container">
    
<!-- bloc-0 -->
<div class="bloc bgc-dark-midnight-blue d-bloc" id="bloc-0">
	<div class="container ">
		<div class="row">
            
				   <div class="col-md-6">
				
				
						<a href="{$homeUrl}" class="is_img">
							<img src="{$baseUrl}/plugins/themes/NUimmersion/templates/images/unimi-bianco.png" alt="{$applicationName|escape}" title="{$applicationName|escape}" style="width:  250px; margin: 10px 0;"/>
						</a>
				
				</div>
				<div class="col-md-6">
					<div class="main-header__admin{if $localeShow} locale-enabled{else} locale-disabled{/if}">
					
								{* User navigation *}
								{capture assign="userMenu"}
									{load_menu name="user" id="navigationUser" ulClass="pkp_navigation_user" liClass="profile"}
								{/capture}
					{* language toggle block *}
													{if $localeShow}
														{include file="frontend/components/languageSwitcher.tpl" id="languageNav"}
													{/if}
										
													{if !empty(trim($userMenu))}
														
														{$userMenu}
													{/if}
					
								
					
							</div>
				</div>
				
				
       </div>
	</div>
</div>
<!-- bloc-0 END -->


<!-- bloc-1 -->
<div class="bloc bg-bg-banner-home b-parallax bg-b-edge l-bloc" id="bloc-1" style="background-image:url({$baseUrl}/plugins/themes/NUimmersion/templates/images/bg_banner_home.jpg);">
	<div class="container bloc-lg">
		<div class="row">
			<div class="col">
				<h1 class="mg-md tc-white">
					Open Journal Systems
				</h1>
			</div>
		</div>
	</div>
</div>
<!-- bloc-1 END -->







        

		


