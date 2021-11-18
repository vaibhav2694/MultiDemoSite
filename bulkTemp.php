 
<!-- BilkFileBackup -->

<div class="uk-width-1"> 
<a href="<?php echo Yii::app()->request->baseUrl; ?>/merchant/FoodItem/Do/Add" class="uk-button"><i class="fa fa-plus"></i> <?php echo Yii::t("default","Add New")?></a>
<a href="<?php echo Yii::app()->request->baseUrl; ?>/merchant/FoodItem" class="uk-button"><i class="fa fa-list"></i> <?php echo Yii::t("default","List")?></a>
<a href="<?php echo Yii::app()->request->baseUrl; ?>/merchant/FoodItem/Do/Sort" class="uk-button"><i class="fa fa-sort-alpha-asc"></i> <?php echo Yii::t("default","Sort")?></a>
<a href="<?php echo Yii::app()->request->baseUrl; ?>/merchant/FooditemBulk" class="uk-button"><i class="fa fa-plus"></i> <?php echo Yii::t("default","Upload Bulk CSV")?></a>
</div>

<div class="spacer"></div>

<p class="right uk-text-muted"><a href="<?php echo baseUrl()."/item-sample.csv"?>" target="_blank"><?php echo t("click here")?></a> <?php echo t("for sample csv format")?></p>
<div class="clear"></div>

<div class="csv-processing-wrap">
<?php
$db_ext=new DbExt;
$msg='';
$error='';
if (isset($_POST) && $_SERVER['REQUEST_METHOD']=='POST'){	
	$filename=$_FILES['file']['name'];	
	if (preg_match("/.csv/i",$filename)) {
	    ini_set('auto_detect_line_endings',TRUE);
		$handle = fopen($_FILES['file']['tmp_name'], "r");
		$x=1;
		$i=0;
		$mtid=Yii::app()->functions->getMerchantID();	
		while (($data = @fgetcsv($handle)) !== FALSE){									    
			if ($i++ <= 1) 
		    {
		        continue;
		    }
			echo "<p class=\"non-indent uk-text-primary\">".t("Processing line")." ($x)<br/></p>";
			if(strlen($data[1])>0) 
			{
				$item_category=explode(',',$data[1]);
				
				$product_list='';
				$j=0;
					if(count($item_category)>=1)
					{
						foreach($item_category as $ic)
						{
							$cats = explode('-',$ic);
							if(count($cats)>1)
							{
								$p_id= Yii::app()->functions->checkFoodCategory($cats[1],$mtid);
							}
							else
							{
								$p_id=Yii::app()->functions->checkFoodCategory($ic,$mtid);
							}
							
							if(is_array($p_id[0])==1)
							{ $product_list[$j++]=$p_id[0]['cat_id']; }
							else
							{ 	$product_list[$j++]=$p_id;	}
						}
					}
					else
					{
						$p_id=Yii::app()->functions->checkFoodCategory($item_category[0],$mtid);
						
						if(is_array($p_id[0])==1)
						{ $product_list[$j++]=$p_id[0]['cat_id']; }
						else
						{ $product_list[$j++]=$p_id; }
				}
				$final_product_list=json_encode($product_list);
				

			if(strlen($data[4])>0) //check the size set or not
			{
				//print_r($data[3]);
				$item_size_temp=explode(',',$data[4]);//cut the string if multiple sizes
				$item_size=array();
				$item_price=array();

				$k=0;
				foreach($item_size_temp as $is)
				{
					$temp=explode("-",$is);
					//print_r(explode("-",$is));die();
					if(isset($temp[0]))
					{
						if(isset($temp[1])){
							$item_price[$k]=$temp[1];	
						}

						$item_size[$k]=$temp[0];
					}
					$k++;
				}
				
				$size_list=array();
				$price_list='{';
				$j=0;//to product list count
				if(count($item_size)>1){
					//this function will return size_id if the category is present
					foreach($item_size as $is)
					{
						
						if($j!=0)
						{
							$price_list.=',';
						}
						$s_id=Yii::app()->functions->checkFoodSize($is,$mtid);
						
						if(is_array($s_id[0])==1)
						{ $size_list[$j]=$s_id[0]['size_id']; }
						else
						{ $size_list[$j]=$s_id; }
						//$size_list[$j]=$s_id[0]['size_id'];
						$price_list.='"'.$size_list[$j].'":"'.($item_price[$j]>0?$item_price[$j]:0).'"';
						$j++;	
					}
				}
				else{
					$s_id=Yii::app()->functions->checkFoodSize($item_size[0],$mtid);
					
						if(is_array($s_id[0])==1)
						{ $size_list[$j]=$s_id[0]['size_id']; }
						else
						{ $size_list[$j]=$s_id; }
						//$size_list[$j]=$s_id[0]['size_id'];
						$price_list.='"'.$size_list[$j].'":"'.($item_price[$j]>0?$item_price[$j]:0).'"';
				}
				$final_size_list=json_encode($size_list);
				$final_price_list=$price_list.'}';
			
			}


			// code 
             if(strlen($data[6])>0) 
			{
				$addon_category=explode(',',$data[6]);
				
				//dump($addon_category);
				$addon_category_list =array();
				$j=0;
					if(count($addon_category)>=1)
					{
						foreach($addon_category as $ac)
						{
							$adcats = explode(',',$ac);

							//dump($adcats);
							if(count($adcats)>1)
							{
								$ad_cat= Yii::app()->functions->checkFoodAddonCat($adcats[1],$mtid);
								

							}else
							{
								$ad_cat=Yii::app()->functions->checkFoodAddonCat($ac,$mtid);
								
							}
							
							if(is_array($ad_cat[0])==1)
							{ $addon_category_list[$j++]=$ad_cat[0]['subcat_id']; }
							else
							{ 	$addon_category_list[$j++]=$ad_cat; 	}
						}
					}
					else
					{
						$ad_cat=Yii::app()->functions->checkFoodAddonCat($addon_category[0],$mtid);
						
						
						if(is_array($ad_cat[0])==1)
						{ $addon_category_list[$j++]=$ad_cat[0]['subcat_id']; }
						else
						{ $addon_category_list[$j++]=$ad_cat;  }
				}
				$addon_cats=json_encode($addon_category_list);
                     

				//print_r($tm);
					// addon item 


                                if(strlen($data[7])>0) 
			                    {
			                    	//dump($data[7]);
				                    $addon_item=explode(',',$data[7]);
                                    $addon_item_list ='';
				                    $t=0;
									if(count($addon_item)>=1)
									{
										foreach($addon_item as $ik => $ai)
										{
											$adItem = explode('-',$ai); 
                                            if(count($adItem)>1)
							                {
							        foreach ($addon_category_list as $ck => $vals) {
							                		//dump($vals);
							                	if($ik == $ck){
                                  $ai_id= Yii::app()->functions->checkFoodAddonItem($adItem[0],$adItem[1],$mtid,$vals);
                                            }

                                          }
							                }
											else
											{

										foreach ($addon_category_list as $ck => $vals) {
							                		//dump($vals);
							                	if($ik == $ck){
												$ai_id=Yii::app()->functions->checkFoodAddonItem($ai,$mtid);
											    }
											      }
											}
$addon_item_list = array();
											if(is_array($ai_id[0])==1)
											{ $addon_item_list[$t++][]=$ai_id[0]['sub_item_id']; }
											else
											{ 	$addon_item_list[$t++][]=$ai_id; 	}



									    }


				                }else{
					$ai_id=Yii::app()->functions->checkFoodAddonItem($addon_item[0],$mtid);
						
						if(is_array($ai_id[0])==1)
						{ $addon_item_list[$t++][]=$ai_id[0]['sub_item_id']; }
						else
						{ $addon_item_list[$t++][]=$ai_id; }
				       
				        }
				       
				      //print_r($addon_category_list);
					$n = array_combine($addon_category_list, $addon_item_list);
					$final_color_list = json_encode($n);
					

                      

                   }
                 
                  $tm = array();
								foreach ($addon_category_list as $key => $tt) {
					       $tm[$tt][] = 2 ;

                   
				}

                 
			}

          

			// end 
      			    	
		    	echo "<p class=\"indent uk-text-primary\">".t("Saving Menu")."...</p>";
		    	$res1=Yii::app()->functions->isMenuExist($data[0],$final_product_list,$mtid);
		    	if(!$res1){
		    		//print_r($tm);
		    		$params=array(
                  'date_created'=>FunctionsV3::dateNow(),
                  'ip_address'=>$_SERVER['REMOTE_ADDR'],
                  'merchant_id'=>Yii::app()->functions->getMerchantID(),
								  'item_name'=>isset($data[0])?$data[0]:"",
								  'item_description'=>isset($data[2])?$data[2]:'',
								  'status'=>($data[3]==1?'publish':'unpublish'),
								  'category'=>$final_product_list,
								  'price'=>$final_price_list,
								  'addon_item'=>$final_color_list,
								  'require_addon'=>json_encode($tm),
								  'cooking_ref'=>"",
								  'discount'=>isset($data[5])?$data[5]:"0",
								  'multi_option'=>"",
								  'multi_option_value'=>"",
								  'photo'=>"",
								  'ingredients'=>"",
								  'spicydish'=>"0",
								  'two_flavors'=>'0',
								  'two_flavors_position'=>"",
								  'dish'=>'',
								  'non_taxable'=>1,
								  'gallery_photo'=>""
								  );
						if ( Yii::app()->functions->insertMenu($params))
						{

							// code for version 545 start

	               

			    	 // Code for version 545 end 


							echo "<p class=\"indent uk-text-primary\">".t("Successful")."...</p>";
						} 
						else 
						{
	 		    		   echo "<p class=\"indent uk-text-danger\">".t("Failed1")."...</p>";
			    	}		

			    
		    	}
		    	else
		    	{
		    		    $params=array(
			                  'date_created'=>FunctionsV3::dateNow(),
			                  'ip_address'=>$_SERVER['REMOTE_ADDR'],
			                  'merchant_id'=>Yii::app()->functions->getMerchantID(),
											  'item_name'=>isset($data[0])?$data[0]:"",
											  'item_name_search'=>isset($data[0])?$data[0]:"",
											  'item_description'=>isset($data[2])?$data[2]:'',
											  'status'=>($data[3]==1?'publish':'unpublish'),
											  'category'=>$final_product_list,
											  'price'=>$final_price_list,
											  'addon_item'=>$final_color_list,
											  'require_addon'=>json_encode($tm),
											  'discount'=>isset($data[5])?$data[5]:"0",
											  'barcode'=>isset($data[8])?$data[8]:''
								);


			    	// code for version 545 start
						$final_product_list1 = array();
						$final_product_list1 = array_push($final_product_list1, $final_product_list) ;
						ItemClass::insertItemRelationship(
		                Yii::app()->functions->getMerchantID(),
		                (integer)$res1[0]['item_id'],
		                isset($final_product_list1)?(array)$final_product_list1:''
	                );	
			
						$addon_item1 = array();
						$addon_item1 = array_push($addon_item1, $addon_item) ;
							     ItemClass::insertItemRelationshipSubcategory(
		                Yii::app()->functions->getMerchantID(),
		                (integer)$res1[0]['item_id'],
		                isset($addon_item1)?(array)$addon_item1:''
	                );
					 $final_price_list = json_decode($final_price_list,true);
					 $sizeArray= array_keys($final_price_list);
					 $PriceArray= array_values($final_price_list);
	                ItemClass::insertItemRelatinship(
	                  Yii::app()->functions->getMerchantID(),
		              (integer)$res1[0]['item_id'],
		              array(
		               'size'=>isset($sizeArray)?(array)$sizeArray:'',
		               'price'=>isset($PriceArray)?(array)$PriceArray:''
		              )		              
	                );
	                	
				                     
								Item_translation::insertTranslation( 
								(integer) $res1[0]['item_id'],
								'item_id',
								'item_name',
								'item_description',
								array(	                  
								  'item_name'=>isset($data[0])?$data[0]:'',
								  'item_description'=>isset($data[2])?$data[2]:'',
								),"{{item_translation}}");


			    	 // Code for version 545 end 

			    		 if ( Yii::app()->functions->updateMenu($params,$res1[0]['item_id'])){
			    		echo "<p class=\"indent uk-text-primary\">".t("Update Successful")."...</p>";
	                      }else {
	 		    		   echo "<p class=\"indent uk-text-danger\">".t("Failed")."...</p>";
			    	    }
		    	}
		    	
		    } else echo "<p class=\"indent uk-text-danger\">".t("Error on line"." ".$x)." <br/></p>";
		    $x++;
		}	
		ini_set('auto_detect_line_endings',FALSE);					
	} else $msg=t("Please upload a valid CSV file");
}
?>
	</div>
	<form class="uk-form uk-form-horizontal" method="post" enctype="multipart/form-data"  >


<?php if ( !empty($msg)):?>
<p class="uk-alert uk-alert-danger"><?php echo $msg;?></p>
<?php endif;?>

<div class="uk-form-row">
  <label class="uk-form-label"><?php echo Yii::t("default","CSV")?></label>
  <input type="file" name="file" id="file" />
</div>

<div class="uk-form-row">
<label class="uk-form-label"></label>
<input type="submit" value="<?php echo Yii::t("default","Submit")?>" class="uk-button uk-form-width-medium uk-button-success">
</div>


</form>














