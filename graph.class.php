<?



	/**
	 * Generate Line / Bar graphs with multiple sets of data
	 * 
	 * @version 1.0 (Work In Progress)
	 * @package Graph Generator
	 * @author Stephen Speakman (www.stephen-speakman.info)
	 * @copyright Copyright (c) 2009 Stephen Speakman 2009
	 */

	/**
	 * Graph Generator
	 * 
	 * @package Graph Generator
	 */

	class Graph
	{

		var $graph;		// Holder for the graph	
		var $graphHeight;	// The height of the graph
		var $graphWidth;	// The width of the graph
		var $graphColour;	// The background colour of the graph
		var $graphType;		// The type of graph to draw (Bar Graph, Line Graph)
		
		var $graphTitle;	// The title of the graph
		var $xAxisTitle;	// The title of the X-Axis
		var $yAxisTitle;	// The title of the Y-Axis
		var $graphFont;		// The font file for the graph
		
		var $data;		// The first set of data for our graph (As an array)
		var $data2;		// The second set of data for our graph (As an array)
		var $dataColour;	// The colour for our first dataset (Blank = Default / Random)
		var $data2Colour;	// The colour for our second dataset (Blank = Default / Random)
			

		function __construct($height=400, $width=600, $title=NULL, $font='arial.ttf')
		{
			// Set the font for the graph titles
			$this->graphFont = $font;
		
			if(!empty($title))
			{
				// Set the graph title
				$this->graphTitle = $title;	
			}		
					
			
			if(is_numeric($height) && is_numeric($width))
			{
				// Define the width and height for the graph
				$this->graphHeight 	= $height;
				$this->graphWidth 	= $width;
			}
			
			// Create our image placeholder and set the default background colour
			$this->graph = imagecreatetruecolor($this->graphWidth, $this->graphHeight);
			$this->addData();
			$this->setGraphColour();
			$this->setDataColour();

		}
		
		function addData($data=array(), $data2=array())
		{
			// Testing: How many points / bars to generate
			$rand = rand(5, 30);
			
			if(count($data) > 0)
			{
				// Set the data for the graph
				$this->data = $data;
			}
			else
			{
				// Generate random data for the graph				
				$this->data = $this->generateData($rand);
			}
			
			// Note: Do not enforce 2nd set upon completion.

			if(count($data2) > 0)
			{
				// Set the data for the graph
				$this->data2 - $data;
			}
			else
			{
				// Generate random data for the graph (For Now)
				$this->data2 = $this->generateData($rand);
			}
		}
		
		function setDataColour($dataColour=array(), $data2Colour=array())
		{
			if(count($dataColour) == 3)
			{
				// Set New Colour
				$this->dataColour = imagecolorallocate($this->graph, $dataColour[0], $dataColour[1], $dataColour[2]);
			}
			else
			{
				// Use Default
				$this->dataColour = imagecolorallocate($this->graph, 170, 170, 170);
			}
			
			if(count($data2Colour) == 3)
			{
				// Set New Colour
				$this->data2Colour = imagecolorallocate($this->graph, $data2Colour[0], $data2Colour[1], $data2Colour[2]);
			}
			else
			{
				// Use Default
				$this->data2Colour = imagecolorallocate($this->graph, 230, 230, 230);
			}
		}

		function setTitles($yTitle=NULL, $xTitle=NULL)
		{
			if(!empty($yTitle))
			{
				// Set the Y-Axis Title
				$this->yAxisTitle = $yTitle;
			}
			
			if(!empty($xTitle))
			{
				// Set the X-Axis Title
				$this->xAxisTitle = $xTitle;
			}
		}

		function setGraphColour($red=255, $green=255, $blue=255)
		{
			if(is_numeric($red) && is_numeric($green) && is_numeric($blue))
			{
				// Set the background colour of the graph
				$this->graphColour = imagecolorallocate($this->graph, $red, $green, $blue);
			}
		}
			
		
		function drawLineGraph()
		{
			// Set the text colour for our y and x axis as well as the title
			$black 		= imagecolorallocate($this->graph, 0, 0, 0);
			$grey		= imagecolorallocate($this->graph, 170, 170, 170);
			$white		= imagecolorallocate($this->graph, 230, 230, 230);
			$line		= imagecolorallocate($this->graph, 240, 240, 240);
			
			// Store our data as a temporary variable
			$data_temp 	= $this->data;
			$data_temp2 	= $this->data2;
			
			// Fill our graph with the background colour (default is white)
			imagefilledrectangle($this->graph, 0, 0, $this->graphWidth, $this->graphHeight, $this->graphColour);	
			
			// Add a title to the graph and position it correctly (If Valid)
			if(!empty($this->graphTitle))
			{
				$size 		= imagettfbbox(10, 0, $this->graphFont, $this->graphTitle);
				$long_text 	= $size[2]+$size[0];
				$posx		= (35+($this->graphWidth-$long_text)/2);
				imagettftext($this->graph, 10, 0, $posx, 20, $black, $this->graphFont, $this->graphTitle);		
			}
			
			// Add the Y-Axis Title and position it correctly (If Valid)
			if(!empty($this->yAxisTitle))
			{
				$size 		= imagettfbbox(10, 90, $this->graphFont, $this->yAxisTitle);
				$long_text 	= ($size[3]+$size[0]);
				$posy		= (35+(($this->graphHeight-35)-$long_text)/2);
				imagettftext($this->graph, 10, 90, 20, $posy, $black, $this->graphFont, $this->yAxisTitle);
			}
			
			// Add the X-Axis Title and position it correctly (If Valid)
			if(!empty($this->xAxisTitle))
			{
				$size 		= imagettfbbox(10, 0, $this->graphFont, $this->xAxisTitle);
				$long_text 	= $size[2]+$size[0];
				$posx		= (35+($this->graphWidth-$long_text)/2);
				imagettftext($this->graph, 10, 0,  $posx, ($this->graphHeight-10), $black, $this->graphFont, $this->xAxisTitle);
			}
			
			// Find the highest value in our data
			rsort($data_temp, SORT_NUMERIC);
			rsort($data_temp2, SORT_NUMERIC);
			
			if($data_temp2[0] > $data_temp[0])
			{
				$data_temp[0] = $data_temp2[0];	
			}
			
			// Calculate the bar width
			$barWidth 	= ((($this->graphWidth-35)/count($this->data))-1);
			$height 	= (($this->graphHeight-70)/10);
			$y1 		= 35;
			
			// Values for the Y-Axis
			$values 	= array(0, 200, 180, 160, 140, 120, 100, 80, 60, 40, 20, 0);
			$needles 	= array(180, 140, 100, 60, 20);
			
			// Add lines and labels along Y Axis
			for($i=1;$i<=11;$i++)
			{
				
					if($i != 1) {
						$y1 = $y1+$height;
					}
									
					imageline($this->graph, 38, $y1, 40, $y1, $black);
					imageline($this->graph, 40, $y1, 600, $y1, $line);
					
					$size 		= imagettfbbox(8, 0, $this->graphFont, $values[$i]);
					$long_text 	= $size[2]+$size[0];
					$posx		= (23-($long_text/2));
					$y2 		= ($y1+4);
				
				if(!in_array($values[$i], $needles)
				 {
					imagettftext($this->graph, 8, 0, $posx, $y2, $black, $this->graphFont, $values[$i]);
				}
			}

			// Add lines and labels along X Axis as well as secondary data
			for($i = 0; $i < count($this->data2); $i++)
			{
			
				$colour 	= $this->generateRandomColour();

				$x1		= ($i == 0) ? 40 : $x2;
				$x2		= ($i == 0) ? 40 : ($x1+$barWidth);
				$y1		= ($i == 0) ? ($this->graphHeight-35) : $y2;
				$y2		= 35 + (($this->graphHeight-35)-($this->data2[$i]/($data_temp[0]/100))*(($this->graphHeight-35)/100));
				
			
				imageline($this->graph, $x2, ($this->graphHeight-35), $x2, ($this->graphHeight-33), $black);
				
				$size 		= imagettfbbox(8, 0, $this->graphFont, $i);
				$long_text 	= $size[2]+$size[0];
				$posx		= ($x2-($long_text/2));
				
				imagettftext($this->graph, 8, 0, $posx, ($this->graphHeight-20), $black, $this->graphFont, $i);
				
				$coordinates = array(
					$x1, $y1,
					$x2, $y2,
					$x2, ($this->graphHeight-35),
					$x1, ($this->graphHeight-35)
				);
				
				$fillcolour = imagecolorallocatealpha($this->graph, 231, 23, 24, 90);

				imagefilledpolygon($this->graph, $coordinates, 4, $fillcolour);
				imageline($this->graph, $x1, $y1, $x2, $y2, $this->data2Colour);

				if($i != 0)
				{
					imagefilledrectangle($this->graph, ($x2-1), ($y2-1), ($x2+1), ($y2+1), $this->data2Colour);
				}
			}
	
			// Plot the lines
			for($i = 0; $i < count($this->data); $i++)
			{
				
				$colour = $this->generateRandomColour();

				$x1		= ($i == 0) ? 40 : $x2;
				$x2		= ($i == 0) ? 40 : ($x1+$barWidth);
				$y1		= ($i == 0) ? ($this->graphHeight-35) : $y2;
				$y2		= 35 + (($this->graphHeight-35)-($this->data[$i]/($data_temp[0]/100))*(($this->graphHeight-35)/100));

				$coordinates = array(
					$x1, $y1,
					$x2, $y2,
					$x2, ($this->graphHeight-35),
					$x1, ($this->graphHeight-35)
				);

				$fillcolour = imagecolorallocatealpha($this->graph, 24, 213, 11, 90);
				imagefilledpolygon($this->graph, $coordinates, 4, $fillcolour);

				// Create a rectangle for the bar
				imageline($this->graph, $x1, $y1, $x2, $y2, $this->dataColour);

				if($i != 0)
				{
					imagefilledrectangle($this->graph, ($x2-1), ($y2-1), ($x2+1), ($y2+1), $this->dataColour);
				}
			}
			
			// Draw our X and Y Axis
			imageline($this->graph, 40, 35, 40, ($this->graphHeight-35), $black);
			imageline($this->graph, 40, ($this->graphHeight-35), $this->graphWidth, ($this->graphHeight-35), $black);
		}
		
		function renderGraph()
		{
			// Render our image and display it on the screen
			header("Content-Type: image/png");
			imagepng($this->graph);
			imagedestroy($this->graph);
		}
		
		function generateRandomColour()
		{
			// Generate random values for the colour
			$red 	= rand(0, 255);
			$green 	= rand(0, 255);
			$blue 	= rand(0, 255);
			
			// Set the new colour
			$colour = imagecolorallocate($this->graph, $red, $green, $blue);
			return $colour;
		}
		
		function generateData($count=17)
		{
			// Array to hold the data
			$data = array();
			
			for($i = 0; $i < $count; $i++)
			{
				// Select random value between 50 and 250
				$data[] = rand(50, 250);
			}
			
			return $data;
		}
	}

?>

