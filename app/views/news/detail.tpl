<h2>{$news->getPublishedAt()|format_date} {$news->getTitle()}</h2>

{$news->getBody()|markdown nofilter} 
