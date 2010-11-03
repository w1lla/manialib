<?php
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO
 */

$request = RequestEngineMVC::getInstance();
$response = ResponseEngine::getInstance();

View::render('Examples', '_navigation');

$layout = new FlowLayout(70, 70);
$layout->setMargin(3, 3);

Manialink::beginFrame(-18, 35, 1, null, $layout);
{
	$layout = new NullLayout(30, 30);
	Manialink::beginFrame(0, 0, 1, null, $layout);
	{
		////////////////////////////////////////////////////////////////////////
		// ColumnLayout example
		////////////////////////////////////////////////////////////////////////
		
		$ui = new Panel(30, 30);
		$ui->title->setText('ColumnLayout');
		$ui->save();
		
		$layout = new ColumnLayout(30, 30);
		$layout->setMarginHeight(1);
		$layout->setBorder(1, 6);
		
		Manialink::beginFrame(0, 0, 1, null, $layout);
		{
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		}
		Manialink::endFrame();
	}
	Manialink::endFrame();
	
	$layout = new NullLayout(30, 30);
	Manialink::beginFrame(0, 0, 1, null, $layout);
	{
		////////////////////////////////////////////////////////////////////////
		// LineLayout example
		////////////////////////////////////////////////////////////////////////
		
		$ui = new Panel(30, 30);
		$ui->title->setText('LineLayout');
		$ui->save();
		
		$layout = new LineLayout(30, 30);
		$layout->setMarginWidth(1);
		$layout->setBorder(1, 6);
		
		Manialink::beginFrame(0, 0, 1, null, $layout);
		{
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		}
		Manialink::endFrame();
	}
	Manialink::endFrame();

	$layout = new NullLayout(30, 30);
	Manialink::beginFrame(0, 0, 1, null, $layout);
	{
		////////////////////////////////////////////////////////////////////////
		// FlowLayout example
		////////////////////////////////////////////////////////////////////////
		
		$ui = new Panel(30, 30);
		$ui->title->setText('FlowLayout');
		$ui->save();
		
		$layout = new FlowLayout(30, 30);
		$layout->setMargin(1, 1);
		$layout->setBorder(1, 6);
		
		Manialink::beginFrame(0, 0, 1, null, $layout);
		{
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
			
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
			
			$ui = new Quad(5, 5);
			$ui->save();
			
			$ui = new Quad(5, 5);
			$ui->save();
			
			$ui = new Quad(5, 5);
			$ui->save();
		}
		Manialink::endFrame();
	}
	Manialink::endFrame();

	$layout = new NullLayout(30, 30);
	Manialink::beginFrame(0, 0, 1, null, $layout);
	{
		////////////////////////////////////////////////////////////////////////
		// FlowLayout example 2
		////////////////////////////////////////////////////////////////////////
		
		$ui = new Panel(30, 30);
		$ui->title->setText('FlowLayout');
		$ui->save();
		
		$layout = new FlowLayout(30, 30);
		$layout->setMargin(1, 1);
		$layout->setBorder(1, 6);
		
		Manialink::beginFrame(0, 0, 1, null, $layout);
		{
			$ui = new Quad(5, 1);
			$ui->save();
		
			$ui = new Quad(5, 2);
			$ui->save();
		
			$ui = new Quad(5, 3);
			$ui->save();
			
			$ui = new Quad(5, 4);
			$ui->save();
		
			$ui = new Quad(5, 5);
			$ui->save();
		
			$ui = new Quad(1, 5);
			$ui->save();
			
			$ui = new Quad(2, 5);
			$ui->save();
			
			$ui = new Quad(3, 5);
			$ui->save();
			
			$ui = new Quad(4, 5);
			$ui->save();
			
			$ui = new Quad(5, 5);
			$ui->save();
			
			$ui = new Quad(35, 5);
			$ui->save();$ui = new Quad(35, 5);
			$ui->save();
		}
		Manialink::endFrame();
	}
	Manialink::endFrame();
}
Manialink::endFrame();

?>