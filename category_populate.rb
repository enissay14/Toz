require 'json'
require 'watir'


categories = JSON.parse(File.read('categories.json'))
startingElement = 5

browser = Watir::Browser.new
browser.goto("http://www.tianjinexpats.com/administrator/index.php?option=com_categories&view=category&layout=edit&extension=com_content") 


browser.text_field(:id , "mod-login-username").set '**'
browser.text_field(:id , "mod-login-password").set '##'
browser.button(:class , "btn-large").click

i=0
categories.each do |category|
	
	if i > startingElement
		
		
		browser.goto("http://www.tianjinexpats.com/administrator/index.php?option=com_categories&view=category&layout=edit&extension=com_content") 
		
		
		jformTitle = category[1]
		jformAlias = category[2]
		browser.text_field(:id , "jform_title").set jformTitle
		browser.text_field(:id , "jform_alias").set jformAlias
		
		
		browser.div(:id , "jform_parent_id_chzn").click
		browser.ul(:class , "chzn-results").li(:index => 11).click

		
		browser.button(:class , "btn-success").click

	end
	i = i +1
end
