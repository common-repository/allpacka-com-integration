<?php

class AP_MergePdf{

	public static function merge( $files, $destination, $outputPath ){

		$pdf = new FPDI();
        
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
        
		self::join($pdf, $files);
        
		$pdf->Output($outputPath, $destination);
        
	}
	
	private static function join($pdf, $fileList){
	   
		if( empty($fileList) || !is_array($fileList) ){
		  
			die( __( 'Error while generating label!', 'allpacka' ) );
            
		}
		
		foreach($fileList as $file){
		  
			self::addFile($pdf, $file);
            
		}
	}
	
	private static function addFile( $pdf, $file ){
	   
		$numPages = $pdf->setSourceFile( $file );
		
		if( empty($numPages) || $numPages < 1 ){
		  
			return;
            
		}
		
		for( $x = 1; $x <= $numPages; $x++ ){

            $pdf->AddPage( "L", "A6" );
			$pdf->useTemplate( $pdf->importPage($x), null, null, 0, 0, true );
			$pdf->endPage();
            
		}
        
	}
}