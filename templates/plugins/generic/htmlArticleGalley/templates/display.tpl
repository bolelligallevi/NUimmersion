{**
 * plugins/generic/htmlArticleGalley/display.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Embedded viewing of a HTML galley.
 *}
<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{capture assign="pageTitleTranslated"}{translate key="article.pageTitle" title=$article->getLocalizedTitle()|escape}{/capture}
{include file="frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape} pkp_op_{$requestedOp|escape}">

	{* Header wrapper *}
	<header class="main__header html-galley__header">

		<a href="{url page="article" op="view" path=$article->getBestArticleId()}" class="return">
			<span class="sr-only">
				{translate key="article.return"}
			</span>
		</a>

		<a href="{url page="article" op="view" path=$article->getBestArticleId()}" class="title">
			{$article->getLocalizedTitle()|escape}
		</a>
	</header>

<div id="htmlContainer" class="galley_view">
	<iframe id="htmlGalleyFrame" name="htmlFrame" src="{url page="article" op="download" path=$article->getBestArticleId()|to_array:$galley->getBestGalleyId() inline=true}" allowfullscreen webkitallowfullscreen></iframe>
</div>
{call_hook name="Templates::Common::Footer::PageFooter"}

</body>
</html>
