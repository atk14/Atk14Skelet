<h1>{$page_title}</h1>

{render partial="shared/form"}

{if $cleaned_data}
	{render partial="dump_file" field_name=image file=$cleaned_data.image}
	{render partial="dump_file" field_name=file2 file=$cleaned_data.file2}
	{render partial="dump_file" field_name=file3 file=$cleaned_data.file3}
	{dump var=$cleaned_data}
{/if}
