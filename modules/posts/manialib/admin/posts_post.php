<?php
/**
 * Admin page to create a new post
 * @author Maxime Raoust
 * @package posts
 */
 
require_once( dirname(__FILE__) . "/../core.inc.php" );

AdminEngine::checkAuthentication();

////////////////////////////////////////////////////////////////////////////////
// Processing
////////////////////////////////////////////////////////////////////////////////

$session = SessionEngine::getInstance();
$request = RequestEngine::getInstance();

$currentStep = $request->get("step", 1);
$isEditing = (bool) $session->get("post_editing", false);

$steps = array (
	1 => __("choose_a_type"),
	2 => __("write_content"),
	3 => __("add_meta_tags"),
	4 => __("publish") . "!"
);

$post = $session->get("post_object");
if($post)
{
	$post = unserialize(rawurldecode($post));
}
else
{
	$post = new Post;
	$post->setAuthor($session->get("login"));
}

switch($currentStep)
{
	// Choose a type
	case 1:
	
	break;
	
	// Save type
	case 2:
		$post->setPostType(intval($request->get("post_type", 0)));
		$request->delete("post_type");
		
	break;
	
	// Save content & title
	case 3 :
		$post->setTitle($request->get("post_title"));
		$post->setContent($request->get("post_content"));
		$request->delete("post_title");
		$request->delete("post_content");
		
	break;
	
	// Save tags
	case 4 :
		for($i=1; $i<=10; $i++)
		{
			$request->delete("meta_tag_name$i");
			$request->delete("meta_tag_value$i");
			
			if($tagName = $request->get("meta_tag_name$i"))
			{
				if($tagValue = $request->get("meta_tag_value$i"))
				{
					$post->addMetaTag($tagName, $tagValue);
				}
			}
		}
	break;
	
	// Publish
	case 5:
		$post->dbUpdate();
		unset($post);
		$session->delete("post_object");
		$session->delete("post_editing");
		$request->redirectManialink("posts_manage.php");
	break;
	
	// Default
	default: 
		unset($post);
		$session->delete("post_object");
}

if(isset($post))
	$session->set("post_object", rawurlencode(serialize($post)));

////////////////////////////////////////////////////////////////////////////////
// GUI
////////////////////////////////////////////////////////////////////////////////

require_once( APP_PATH . "header.php" );

// Begin navigation
$ui = new Navigation;
if($isEditing)
{
	$ui->title->setText(__("edit_post"));
}
else
{
	$ui->title->setText(__("new_post"));
}
$ui->subTitle->setText(__("manage_posts"));
$ui->logo->setSubStyle("Paint");

foreach($steps as $stepId=>$stepName)
{
	if($stepId == $currentStep)
	{
		$style = '$i$ff0';
	}
	else
	{
		$style = '$999';
	}
	
	$ui->addItem();
	$ui->lastItem()->icon->setSubStyle(null);
	$ui->lastItem()->text->setText($style.$stepName);

}

$ui->quitButton->setManialink($request->createLinkArgList("posts.php"));
$ui->save();
// End navigation

Manialink::beginFrame(-34, 48, 1);
	
	Manialink::beginFrame(49,-5, 1);
		
		switch($currentStep)
		{
			// Choose a type
			case 1:
				$ui = new Panel(80, 80);
				$ui->setHalign("center");
				$ui->title->setText(__("post_type"));
				$ui->save();
				
				$ui = new Quad(40, 60);
				$ui->setHalign("center");
				$ui->setPosition(0, -10, 1);
				$ui->setSubStyle("BgCardList");
				$ui->save();
				
				$i = 0;
				
				foreach(PostsStructure::getPostTypes() as $postTypeId=>$postTypeName)
				{
					$style = '$o';
					if($post->getPostType() == $postTypeId)
					{
						$style .= '$ff0';
					}
					
					$request->set("post_type", $postTypeId);
					$request->set("step", $currentStep+1);
					$link = $request->createLink("posts_post.php");

					Manialink::beginFrame(0, -11-5*$i, 2);
						
						$ui = new Quad(38, 5);
						$ui->setHalign("center");
						$ui->setSubStyle("BgCardSystem");
						$ui->setManialink($link);
						$ui->save();
						
						$ui = new Label(50);
						$ui->setAlign("center", "center");
						$ui->setPosition(0, -2.5, 1);
						$ui->setTextColor("000");
						$ui->setTextSize(2);
						$ui->setText($style . $postTypeName);
						$ui->save();
					
					Manialink::endFrame();
					
					$i++;
				}
			break;
			
			// Write content
			case 2:
				$ui = new Panel(80, 80);
				$ui->setHalign("center");
				$ui->title->setText(__("write_content"));
				$ui->save();
				
				$ui = new Label;
				$ui->setPosition(-36, -7, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("title"));
				$ui->save();
				
				$ui = new Entry(72);
				$ui->setPosition(-36, -11, 1);
				$ui->setName("title");
				$ui->setDefault($post->getTitle());
				$ui->save();
				
				$request->set("post_title", "title");
				
				$ui = new Label;
				$ui->setPosition(-36, -20, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("content"));
				$ui->save();
				
				$ui = new Entry(72, 45);
				$ui->setPosition(-36, -24, 1);
				$ui->enableAutonewline();
				$ui->setMaxline(13);
				$ui->setName("content");
				$ui->setDefault($post->getContent());
				$ui->save();
				
				$request->set("post_content", "content");
				
				$request->set("step", $currentStep+1);
				$link = $request->createLink("posts_post.php");
				
				$ui = new Button;
				$ui->setHalign("center");
				$ui->setPosition(0, -72, 1);
				$ui->setText("Continue");
				$ui->setManialink($link);
				$ui->save();
				
			break;
			
			// Add tags
			case 3:
				
				$tags = $post->getAllMetaTags();
				
				
				$ui = new Panel(80, 80);
				$ui->setHalign("center");
				$ui->title->setText(__("add_meta_tags"));
				$ui->save();
				
				$ui = new Label;
				$ui->setPosition(-36, -9, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("meta_tag_name"));
				$ui->save();
				
				$ui = new Label;
				$ui->setPosition(0, -9, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("meta_tag_value"));
				$ui->save();
				
				for($i=1; $i<=10; $i++)
				{
					$name = "";
					$value = "";
					if($tag = current($tags))
					{
						$name = $tag[0];
						$value = $tag[1];
						next($tags);
					}
					
					Manialink::beginFrame(0, -11-5*$i, 1);
					
						$ui = new Entry(34);
						$ui->setPositionX(-36);
						$ui->setName("meta_tag_name$i");
						$ui->setDefault($name);
						$ui->save();
						
						$request->set("meta_tag_name$i", "meta_tag_name$i");
						
						$ui = new Entry(34);
						$ui->setPositionX(0);
						$ui->setName("meta_tag_value$i");
						$ui->setDefault($value);
						$ui->save();
						
						$request->set("meta_tag_value$i", "meta_tag_value$i");
					
					Manialink::endFrame();
				}
				
				$request->set("step", $currentStep+1);
				$link = $request->createLink("posts_post.php");
				
				$ui = new Button;
				$ui->setHalign("center");
				$ui->setPosition(0, -72, 1);
				$ui->setText("Continue");
				$ui->setManialink($link);
				$ui->save();
				
			break;
			
			// Publish
			case 4:
				$ui = new Panel(80, 80);
				$ui->setHalign("center");
				$ui->title->setText(__("publish"));
				$ui->save();
				
				$ui = new Label(60);
				$ui->setHalign("center");
				$ui->setPosition(0, -10, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("post_ready_to_be_published"));
				$ui->save();
				
				$request->set("step", $currentStep+1);
				$link = $request->createLink("posts_post.php");
				
				$ui = new Button;
				$ui->setHalign("center");
				$ui->setPosition(0, -20, 1);
				$ui->setScale(2);
				$ui->setText(__("publish"));
				$ui->setManialink($link);
				$ui->save();
				
			break;
				$ui = new Panel(80, 80);
				$ui->setHalign("center");
				$ui->title->setText(__("published"));
				$ui->save();
				
				$ui = new Label(60);
				$ui->setHalign("center");
				$ui->setPosition(0, -10, 1);
				$ui->setStyle("TextRaceMessage");
				$ui->setText(__("post_successfully_published"));
				$ui->save();
			
			// Default
			default:
		}
	
	Manialink::endFrame();

Manialink::endFrame();

require_once( APP_PATH . "footer.php" );

?>