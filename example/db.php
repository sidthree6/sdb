<?php

/*

SDB PHP Database Development Library v1.0
Copyright 2010, Sidd Panchal
licensed under the GNU-GPL version 3 Licence

*/
$path = NULL;
// Only reports important PHP errors
error_reporting(E_ERROR | E_NOTICE | E_PARSE | E_STRICT);

// Error function will be used to set style for errors which will be producing by SDB

function error($error1,$error2)
{
		$error_s = "<div style=\"font-family:tahoma;background-color: #ffebe8;border: 1px solid #dd3c10;color: #333333;padding: 10px;font-size: 11px;font-weight: bold;width:auto;margin:2px\">";
				$error_e = "</div>";
		$provideObject=false;
		foreach(debug_backtrace($provideObject) as $row)
		{
				$error = $error_s.$row['function']." -> ".$error1." (Line no. ".$row['line'].") : ".$error2." [Filename: ".$row['file']."]".$error_e;
		}
		// Die if user call this function
		die ($error); 
}

function sdb_init($path="")
{
		global $path;
		if($path="")
		{
				$path = $path."/";
		}
		else
		{
				$path = trim($path);
				if(file_exists($path))
						{
								if(is_dir($path))
								{
										$path = $path."/";
								}
								else
								{
										error("Error while initializing database","Path must be directory");
								}
						}
						else
						{
								error("Error while initializing database","Invalid path");
						}
		}
}

function sdb_open_db($dbname)
{
				// Declare database name globally so it will be used by other function
				global $gbl_db,$path;				
				
				// If Another database is currently open then return true
				if(isset($gbl_db) && $gbl_db != $dbname)
				{
						error("Error while Creating Database","Another database is currently running"); 
				}
				
				// If database name does not exist then it will produce an error
				if(!isset($dbname))
				{					
								error("Error while Creating Database","Invalid parameter");
								
				}
				else
				{
						
						$dbname = trim($dbname);
						// Return as an error if database name is empty
						if($dbname == "")
						{
									error("Error while Creating Database","Error in Database name");
						}
						else
						{
								// If database name is Invalid then error will be generated

								if(!preg_match("/^[A-Za-z0-9]{2,20}$/",$dbname))
								{
											error("Error while Creating Database","Database name must contain only letters without any spaces and it can hold only 2 to 20 letters");
								}

								else
								{
										$dbname = $path.$dbname;
										// Check if database exist in the list
										$check = $dbname;
										// Produce an error if database does exist in the list
										if(file_exists($check))
										{		
														chmod($dbname.".minus", 0700);
														$txt=fopen($dbname.".minus","a+");
														chmod($dbname.".minus", 0100);	
														$gbl_db = $dbname;
										}
										else
										{
												$txt=fopen($dbname.".minus","a+");
												chmod($dbname.".minus", 0100);
												$gbl_db = $dbname;
										}

								}

						}

				}
				// Close database file
				fclose($txt);
				
}
// SDB Query function, is based on SQL query and perform different task like creating table,inserting record,delete record, updating record..
function sdb_query($query)
{
		// Cache Database nad return error if database is not being cached
		global $gbl_db;
		
		if(!isset($gbl_db))								
		{
				error("Error in SDB Query","Database has not been selected yet");
		}
		else
		{
				// Return error if function is in the invalid form
				if(!isset($query))
				{
						error("Error in SDB Query","Atleast 1 paramater is needed to perform SDB query");
				}
				else
				{
						$query = trim($query);
						// Perform this task if user action is set to create_table
						
								if(preg_match("/^(CREATE)\s(TABLE)\s(.*?)\s([(])(.*)([)])$/",$query,$match))
								{
										if($match[1] == "CREATE" && $match[2] == "TABLE")
										{
												$table_name = trim($match[3]);
												$writing_query = "";
												$temp_store = array();
												if(!preg_match("/^[A-Za-z0-9\_]{2,50}$/",$table_name))
												{
														error("Error in SDB Query","table name is invalid ('".$table_name."')");
														return FALSE;
												}
												
												$main_query = trim($match[5]);
												
												$split_query = explode(",",$main_query);
												$count_split_query = count($split_query);
												
												for($i=0;$i<=$count_split_query-1;$i++)
												{
														$split_query[$i] = trim($split_query[$i]);
														if(preg_match("/^(.*)\s(PKEY)$/",$split_query[$i],$match_main))
														{
																if(!preg_match("/^[A-Za-z0-9\_]{2,50}$/",$match_main[1]))
																{
																		error("Error in SDB Query","invalid column name ('".$match_main[1]."')");
																		return FALSE;
																}
																else
																{
																		$writing_query .= "table['".$table_name."']['".$match_main[1]."']['".$match_main[2]."']---///---~~~";
																}
														}
														elseif(preg_match("/^(.*)\s(VARCHAR)\(([0-9]*?)\)$/",$split_query[$i],$match_main))
														{
																if(!preg_match("/^[A-Za-z0-9\_]{2,50}$/",$match_main[1]))
																{
																		error("Error in SDB Query","Invalid column name ('".$match_main[1]."')");
																		return FALSE;
																}
																else
																{
																		if($match_main[3] > 255 || $match_main[3] < 1)
																		{
																				error("Error in SDB Query","VARCHAR value must be between 1 to 255");
																				return FALSE;
																		}
																		else
																		{
																				$writing_query .= "table['".$table_name."']['".$match_main[1]."']['".$match_main[2]."(".$match_main[3].")']---///---~~~";
																		}
																}
														}
														elseif(preg_match("/^(.*)\s(INT)\(([0-9]*?)\)$/",$split_query[$i],$match_main))
														{
																if(!preg_match("/^[A-Za-z0-9\_]{2,50}$/",$match_main[1]))
																{
																		error("Error in SDB Query","Invalid column name ('".$match_main[1]."')");
																		return FALSE;
																}
																else
																{
																		if($match_main[3] > 255 || $match_main[3] < 1)
																		{
																				error("Error in SDB Query","INT value must be between 1 to 255");
																				return FALSE;
																		}
																		else
																		{
																				$writing_query .= "table['".$table_name."']['".$match_main[1]."']['".$match_main[2]."(".$match_main[3].")']---///---~~~";
																		}
																}
														}
														elseif(preg_match("/^(.*)\s(TEXT)$/",$split_query[$i],$match_main))
														{
																if(!preg_match("/^[A-Za-z0-9\_]{2,50}$/",$match_main[1]))
																{
																		error("Error in SDB Query","invalid column name ('".$match_main[1]."')");
																		return FALSE;
																}
																else
																{
																		$writing_query .= "table['".$table_name."']['".$match_main[1]."']['".$match_main[2]."']---///---~~~";
																}
														}
														else
														{
																error("Error in SDB Query","Wrong query has been entered ('".$split_query[$i]."')");
																return FALSE;
														}														
														
												}
												chmod($gbl_db.".minus", 0700);
												$txt=fopen($gbl_db.".minus","a+");
												$read = file_get_contents($gbl_db.".minus");
												
												if(preg_match("/^(.*)---\/\/\/---~~~/",$read))
												{												
														if(!sdb_table_exist($table_name))
														{
															fwrite($txt,$writing_query);
															chmod($gbl_db.".minus", 0100);
															fclose($txt);
															return TRUE;
														}
														else
														{
																error("Error in SDB Query","Table already exists ('".$table_name."')");
														}
												}
												else
												{
														fwrite($txt,$writing_query);
														chmod($gbl_db.".minus", 0100);
														fclose($txt);
														return TRUE;
												}
												
										}
										else
										{
												error("Error in SDB Query","Invalid Query");
										}
								}
								elseif(preg_match("/^(INSERT)\s(INTO)\s(.*?)\s(\()(.*?)(\))\s(VALUES)\s(\()(.*)(\))$/",$query,$match))
								{										
										if($match[1] == "INSERT" && $match[2] == "INTO")
										{
												$table_name = trim($match[3]);
												$first_slot = trim($match[5]);
												
												$split_first_slot = explode(",",$first_slot);
												$count_split_first_slot = count($split_first_slot);
												
												for($i=0;$i<=$count_split_first_slot-1;$i++)
												{
														$values = sdb_get_column_value($table_name,$split_first_slot[$i]);
														if($values == "PKEY")
														{
																error("Error in SDB Query","Column value must not be 'PKEY'");
														}
												}
												
												
												
												chmod($gbl_db.".minus", 0700);
												$txt=fopen($gbl_db.".minus","a+");
												$read = file_get_contents($gbl_db.".minus");
												
												if(preg_match("/^(.*)---\/\/\/---~~~/",$read))
												{
																$proceed=0;
																$split_read = explode("---///---~~~",$read);
																$count_split_read = count($split_read);
																$value = array();
																for($a=0;$a<=$count_split_read-1;$a++)
																{
																		$split_read[$a] = trim($split_read[$a]);
																		if(preg_match("/^(table)(\['(.*?)'\])(\['(.*?)'\])(\['(.*?)'\])$/",$split_read[$a],$read_match))
																		{
																				if($table_name == $read_match[3])
																				{
																						for($i=0;$i<=$count_split_first_slot-1;$i++)
																						{																								
																								if($split_first_slot[$i] == $read_match[5])
																								{
																										$proceed = $proceed + 1;
																										$value[$a] = $read_match[7];
																								}																								
																						}
																				}
																		}
																}
																if($proceed != $count_split_first_slot)
																{
																		error("Error in SDB Query","Something is wrong in Inserting query");
																}
																$match[9] = trim($match[9]);
																$split_second_block = preg_split("/','/",$match[9]);
																$count_split_second_block = count($split_second_block);
																
																$split_second_block[$count_split_second_block-1] = remove_last($split_second_block[$count_split_second_block-1]);
																$split_second_block[0] = remove_first($split_second_block[0]);
																$writing_query = "";
																
																if($count_split_second_block != $count_split_first_slot)
																{
																		error("Error in SDB Query","Invalid Query");
																}
																
																$rows = sdb_get_table_column($table_name);
																$count_rows = count($rows);
																
																for($b=0;$b<=$count_rows-1;$b++)
																{
																		$get_val = sdb_get_column_value($table_name,$rows[$b]);
																		if($get_val == "PKEY")
																		{
																				$pkey_id = sdb_count_table_column($table_name,$rows[$b]) + 1;
																				$writing_query .= "table['".$table_name."']['".$rows[$b]."']['".$get_val."'] = '".$pkey_id."'---///---~~~\n";
																		}
																		elseif(preg_match("/^VARCHAR\((.*?)\)$/",$get_val,$matches))
																		{
																				$this_content = "";
																				for($i=0;$i<=$count_split_first_slot-1;$i++)
																				{																						
																						if($split_first_slot[$i] == $rows[$b])
																						{
																							$this_content = htmlspecialchars($split_second_block[$i]);
																							if(strlen($this_content) > $matches[1])
																							{
																									$this_content = substr($this_content,0,$matches[1]);
																							}
																						}
																				}
																				$writing_query .= "table['".$table_name."']['".$rows[$b]."']['".$get_val."'] = '".$this_content."'---///---~~~";
																		}
																		elseif(preg_match("/^INT\((.*?)\)$/",$get_val,$matches))
																		{
																				$this_content = "";
																				for($i=0;$i<=$count_split_first_slot-1;$i++)
																				{
																						if($split_first_slot[$i] == $rows[$b])
																						{
																							$this_content = htmlspecialchars($split_second_block[$i]);
																							if(strlen($this_content) > $matches[1])
																							{

																									$this_content = substr($this_content,0,$matches[1]);
																							}
																						}
																				}
																				$writing_query .= "table['".$table_name."']['".$rows[$b]."']['".$get_val."'] = '".$this_content."'---///---~~~";
																		}
																		elseif(preg_match("/^TEXT$/",$get_val,$matches))
																		{
																				$this_content = "";
																				for($i=0;$i<=$count_split_first_slot-1;$i++)
																				{																						
																						if($split_first_slot[$i] == $rows[$b])
																						{
																							$this_content = htmlspecialchars($split_second_block[$i]);																			
																						}
																				}
																				$writing_query .= "table['".$table_name."']['".$rows[$b]."']['".$get_val."'] = '".$this_content."'---///---~~~";
																		}
																}
																
																
																fwrite($txt,$writing_query);
																chmod($gbl_db.".minus", 0100);
																fclose($txt);
																
												}
												else
												{
														error("Error in SDB Query","Table/Column does not exists");
												}
												
												
										}
										else
										{
												error("Error in SDB Query","Invalid Query");
										}
								}															
								elseif(preg_match("/^(UPDATE)\s(.*?)\s(SET)\s(.*?)\s?=\s?('|\")(.*)('|\")\s(WHERE)\s(.*?)\s?=\s?('|\")(.*)('|\")\s(AND)\s(.*?)\s?=\s?('|\")(.*)('|\")$/",$query,$match))
								{
										if($match[1] == "UPDATE" && $match[3] == "SET")
										{
												
												if(trim($match[8]) != "WHERE" || trim($match[13]) != "AND")
												{
														error("Error in SDB Query","Invalid Query");
												}
												
												$table_name = $match[2];
												
												$first_column = $match[9];
												$second_column = $match[14];
												$first_column_value = htmlspecialchars($match[11]);
												$second_column_value = htmlspecialchars($match[16]);
												
												$set_column = $match[4];
												$set_column_value = $match[6];
												
												if(!sdb_table_exist($table_name))
												{
														error("Error in SDB Query","Table name is invalid ('".$table_name."')");
												}
												elseif(!sdb_column_exist($table_name,$first_column) || !sdb_column_exist($table_name,$second_column) || !sdb_column_exist($table_name,$set_column))
												{
														error("Error in SDB Query","Column name is invalid");
												}
												
												chmod($gbl_db.".minus", 0700);
												$txt=fopen($gbl_db.".minus","a+");
												$read = file_get_contents($gbl_db.".minus");
												
												$explode = explode("---///---~~~",$read);
												$count_explode = count($explode);
												
												$count_columns = sdb_count_table_column($table_name,$first_column);
												
												$first_column_values = sdb_get_column_place($table_name,$first_column);
												$second_column_values = sdb_get_column_place($table_name,$second_column);
												
												$get_columns = sdb_get_table_column($table_name);
												$count_get_columns = count($get_columns);
												for($i=0;$i<=$count_get_columns-1;$i++)
												{
														$column_values[$get_columns[$i]] = sdb_get_column_place($table_name,$get_columns[$i]);																							
												}
												$returns = array();
												$writing_query = "";
												for($i=0;$i<=$count_columns-1;$i++)
												{
														$first_column_values[$i] = trim($first_column_values[$i]);
														for($a=0;$a<=$count_get_columns-1;$a++)
														{
																$get_value = sdb_get_column_value($table_name,$get_columns[$a]);
																if($first_column_values[$i] == $first_column_value)
																{
																
																		$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."']---///---~~~\n";
																		
																		if(!isset($returns[$get_columns[$a]][0]))
																		{
																			$returns[$get_columns[$a]] = array();
																			array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																		}
																		else
																		{
																				array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																		}
																		
																		if($get_columns[$a] == $set_column)
																		{
																			$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$set_column_value."'---///---~~~\n";
																		}
																		else
																		{
																			$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$column_values[$get_columns[$a]][$i]."'---///---~~~\n";
																		}
																}
																else
																{
																		$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$column_values[$get_columns[$a]][$i]."'---///---~~~\n";
																}
														
																
																
														}
												}
												if(count($returns) == 0)
												{
														for($a=0;$a<=$count_get_columns-1;$a++)
														{
																$returns[$get_columns[$a]] = array();
														}
												}
												
												$txt=fopen($gbl_db.".minus","w+");																								
												$succ = fwrite($txt,$writing_query);
												if(!$succ)
												{
															error("Error in SDB Query","Can not write to database file");
												}
												
												
												
										}
										else
										{
												error("Error in SDB Query","Invalid Query");
										}
								}
								elseif(preg_match("/^(UPDATE)\s(.*?)\s(SET)\s(.*?)\s?=\s?('|\")(.*)('|\")\s(WHERE)\s(.*?)\s?=\s?('|\")(.*)('|\")$/",$query,$match))
								{
										$table_name = $match[2];
										$first_column = $match[9];
										$first_column_value = htmlspecialchars($match[11]);
										
										$set_column = $match[4];
										$set_column_value = $match[6];
										
										if(!sdb_table_exist($table_name))
										{
												error("Error in SDB Query","Table name is invalid ('".$table_name."')");
										}
										elseif(!sdb_column_exist($table_name,$first_column) || !sdb_column_exist($table_name,$set_column))
										{
												error("Error in SDB Query","Column name is invalid");
										}
										
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										
										$explode = explode("---///---~~~",$read);
										$count_explode = count($explode);
										
										$count_columns = sdb_count_table_column($table_name,$first_column);
										
										$first_column_values = sdb_get_column_place($table_name,$first_column);
										
										$get_columns = sdb_get_table_column($table_name);
										$count_get_columns = count($get_columns);
										for($i=0;$i<=$count_get_columns-1;$i++)
										{
												$column_values[$get_columns[$i]] = sdb_get_column_place($table_name,$get_columns[$i]);																							
										}
										$returns = array();
										$writing_query = "";
										
										for($i=0;$i<=$count_columns-1;$i++)
												{
														$first_column_values[$i] = trim($first_column_values[$i]);
														$second_column_values[$i] = trim($second_column_values[$i]);
														for($a=0;$a<=$count_get_columns-1;$a++)
														{
																$get_value = sdb_get_column_value($table_name,$get_columns[$a]);
																if($first_column_values[$i] == $first_column_value && $second_column_values[$i] == $second_column_value)
																{
																
																		$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."']---///---~~~\n";
																		
																		if(!isset($returns[$get_columns[$a]][0]))
																		{
																			$returns[$get_columns[$a]] = array();
																			array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																		}
																		else
																		{
																				array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																		}
																		
																		if($get_columns[$a] == $set_column)
																		{
																			$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$set_column_value."'---///---~~~\n";
																		}
																		else
																		{
																			$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$column_values[$get_columns[$a]][$i]."'---///---~~~\n";
																		}
																}
																else
																{
																		$writing_query.="table['".$table_name."']['".$get_columns[$a]."']['".$get_value."'] = '".$column_values[$get_columns[$a]][$i]."'---///---~~~\n";
																}
														
																
																
														}
												}
												if(count($returns) == 0)
												{
														for($a=0;$a<=$count_get_columns-1;$a++)
														{
																$returns[$get_columns[$a]] = array();
														}
												}
												
												$txt=fopen($gbl_db.".minus","w+");																								
												$succ = fwrite($txt,$writing_query);
												if(!$succ)
												{
															error("Error in SDB Query","Can not write to database file");
												}
										
										
								}
								elseif(preg_match("/^(SELECT)\s(.*?)\s(FROM)\s(.*)$/",$query,$match))
								{
										if($match[1] == "SELECT" && $match[3] == "FROM")
										{
											if(preg_match("/\sWHERE\s/",$match[4]))
											{
													if(preg_match("/\s(AND)\s/",$match[4]))
													{
															$split_first = preg_split("/\sWHERE\s/",$match[4]);
															$count_split_first = count($split_first);
															
															$split_first[0] = trim($split_first[0]);
															$split_first[1] = trim($split_first[1]);
															
															if(!sdb_table_exist($split_first[0]))
															{
																	error("Error in SDB Query","Table does not exists ('".$split_first[0]."')");
															}
															
															if(preg_match("/^(.*?)\s?=\s?('|\")(.*)('|\")\s(AND|OR)\s(.*?)\s?=\s?('|\")(.*)('|\")$/",$split_first[1],$match_column))
															{
																	$first_column = $match_column[1];
																	$second_column = $match_column[6];
																	if(!sdb_column_exist($split_first[0],$first_column) || !sdb_column_exist($split_first[0],$second_column))
																	{
																			error("Error in SDB Query","column does not exists");
																	}
																	else
																	{ 
																			$match[2] = trim($match[2]);
																			if($match[2] == "*")
																			{
																					chmod($gbl_db.".minus", 0700);
																					$txt=fopen($gbl_db.".minus","a+");
																					$read = file_get_contents($gbl_db.".minus");
																					
																					$explode = explode("---///---~~~",$read);
																					$count_explode = count($explode);
																					
																					$return = array();
																					
																					$num_first = sdb_count_column_with_value($split_first[0],$first_column,$match_column[3]);
																					$num_second = sdb_count_column_with_value($split_first[0],$second_column,$match_column[8]);;
																					
																					$low = sdb_get_lowest($num_first,$num_second);
																					
																					$count_columns = sdb_count_table_column($split_first[0],$first_column);
																					$counts = count($count_columns);
																					
																					$first_column_values = sdb_get_column_place($split_first[0],$first_column);
																					$first_column_values = $first_column_values;
																					$second_column_values = sdb_get_column_place($split_first[0],$second_column);
																					$second_column_values = $second_column_values;
																					
																					$get_columns = sdb_get_table_column($split_first[0]);
																					$count_get_columns = count($get_columns);
																					for($i=0;$i<=$count_get_columns-1;$i++)
																					{
																							$column_values[$get_columns[$i]] = sdb_get_column_place($split_first[0],$get_columns[$i]);																							
																					}

																					$returns = array();
																																							
																					
																																										
																					for($i=0;$i<=$count_columns-1;$i++)
																					{
																							$first_column_values[$i] = trim($first_column_values[$i]);
																							$second_column_values[$i] = trim($second_column_values[$i]);
																							if($first_column_values[$i] == $match_column[3] && $second_column_values[$i] == $match_column[8])
																							{
																									
																									for($a=0;$a<=$count_get_columns-1;$a++)
																									{
																											
																											if(!isset($returns[$get_columns[$a]][0]))
																											{
																												$returns[$get_columns[$a]] = array();
																												array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																											}
																											else
																											{
																													array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																											}
																											
																											
																									}																		
																									
																									
																							}
																					}
																					
																					if(count($returns) == 0)
																					{
																							for($a=0;$a<=$count_get_columns-1;$a++)
																							{
																									$returns[$get_columns[$a]] = array();
																							}
																					}
																					
																					return($returns);
																					
																			}
																			else
																			{
																					error("Error in SDB Query","SDB is only supports all columns, please use *");
																			}
																	}
															}
															
													}
													else
													{
															$split_first = preg_split("/\sWHERE\s/",$match[4]);
																	$count_split_first = count($split_first);
																	
																	$split_first[0] = trim($split_first[0]);
																	$split_first[1] = trim($split_first[1]);
																	
															if(preg_match("/^(.*?)\s?=\s?('|\")(.*)('|\")$/",$split_first[1],$match_column))
															{
																	
																	
																	$first_column = $match_column[1];
																	if(!sdb_column_exist($split_first[0],$first_column))
																	{
																			error("Error in SDB Query","column does not exists");
																	}
																	else
																	{
																			if($match[2] == "*")
																					{
																							chmod($gbl_db.".minus", 0700);
																							$txt=fopen($gbl_db.".minus","a+");
																							$read = file_get_contents($gbl_db.".minus");
																							
																							$explode = explode("---///---~~~",$read);
																							$count_explode = count($explode);
																							
																							$return = array();
																							
																							$num_first = sdb_count_column_with_value($split_first[0],$first_column,$match_column[3]);
																							
																
																							$count_columns = sdb_count_table_column($split_first[0],$first_column);
																							$counts = count($count_columns);
																							
																							$first_column_values = sdb_get_column_place($split_first[0],$first_column);
																							
																							$get_columns = sdb_get_table_column($split_first[0]);
																							$count_get_columns = count($get_columns);
																							for($i=0;$i<=$count_get_columns-1;$i++)
																							{
																									$column_values[$get_columns[$i]] = sdb_get_column_place($split_first[0],$get_columns[$i]);																							
																							}
		
																							$returns = array();
																																									
																							
																																												
																							for($i=0;$i<=$count_columns-1;$i++)
																							{
																									$first_column_values[$i] = trim($first_column_values[$i]);
																									if($first_column_values[$i] == $match_column[3])
																									{
																											
																											for($a=0;$a<=$count_get_columns-1;$a++)
																											{
																													if(!isset($returns[$get_columns[$a]][0]))
																													{
																														$returns[$get_columns[$a]] = array();
																														array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																													}
																													else
																													{
																															array_push($returns[$get_columns[$a]],$column_values[$get_columns[$a]][$i]);
																													}
																											}																		
																											
																											
																									}
																							}
																							
																							if(count($returns) == 0)
																							{
																									for($a=0;$a<=$count_get_columns-1;$a++)
																									{
																											$returns[$get_columns[$a]] = array();
																									}
																							}
																							
																							return($returns);
																							
																					}
																					else
																					{
																							error("Error in SDB Query","SDB is only supports all columns, please use *");
																					}
																	}
															}else
															{
																 error("Error in SDB Query","Invalid Query");	
															}
													}
													
											}
											else
											{
												if(!sdb_table_exist($match[4]))
												{
														error("Error in SDB Query","Table does not exists ('".$match[2]."')");	
												}
												else
												{
													if($match[2] == "*")
													{
																chmod($gbl_db.".minus", 0700);
																$txt=fopen($gbl_db.".minus","a+");
																$read = file_get_contents($gbl_db.".minus");
																
																$explode = explode("---///---~~~",$read);
																$count_explode = count($explode);
																
																$return = array();
																
																for($i=0;$i<=$count_explode-1;$i++)
																{
																		$explode[$i] = trim($explode[$i]);
																		if(preg_match("/^table\['".$match[4]."'\]\['(.*?)'\]\['(.*?)'\]\s?=\s?(')(.*)(')$/",$explode[$i],$select_match))
																		{
																				if(!isset($return[$select_match[1]][0]))
																				{
																						$return[$select_match[1]] = array();
																						array_push($return[$select_match[1]],$select_match[4]);
																				}
																				else
																				{
																						array_push($return[$select_match[1]],$select_match[4]);
																				}
																		}
																}																
																return($return);
													}													
													else
													{
															error("Error in SDB Query","SDB is only supports all columns, please use *");															
													}
														
												}
											}
										}
										else
										{
												error("Error in SDB Query","Invalid Query");
										}
								}
								else
								{
										error("Error in SDB Query","Invalid Query ('".$query."')");
								}
						
				}
		}
		
		
}

function sdb_num_rows($query)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error in SDB Query","Database has not been selected yet");
		}
		else
		{
				if(!isset($query))
				{
						error("Error in SDB Query","Invalid parameter"); 
				}
				else
				{
						$first = count($query);
						$ret = count($query,COUNT_RECURSIVE);
						$return = $ret - $first;
						$return = $return/$first;
						return($return);
				}
		}
}

function sdb_replace_keywords($string)
{
		if(!isset($string))
		{
				error("Error","Invalid parameter"); 
		}
		else
		{
				$keywords = array("/WHERE/","/AND/","/table/","/SDB/","/SELECT/","/SET/","/UPDATE/","/FROM/","/INSERT/","/INTO/","/CREATE/","/TABLE/","/DELETE/");
				$replace  = array("Where","And","Table","Sdb","Select","Set","Update","From","Insert","Into","Create","Table","Delete");
				return(preg_replace($keywords,$replace,$string));
		}
}

function sdb_get_lowest($one,$two)
{
		if(!isset($one) || !isset($two))
		{
				error("Error","Invalid parameter"); 
		}
		else
		{
				if($one < $two)
				{
						return $one;
				}
				elseif($one > $two)
				{
						return $two;
				}
				else
				{
						return $one;
				}
		}
}

function sdb_backup($db)
{
		
				if(!isset($db))
				{
						error("Error while checking column","Invalid parameter");
				}
				else
				{
						chmod($db.".minus", 0700);
						$txt=fopen($db.".minus","a+");
						$read = file_get_contents($db.".minus");
						echo "\n\n".$read."\n\n";
				}
		
}

function sdb_get_column_place($table,$column)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column))
				{
						error("Error while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								if(!sdb_column_exist($table,$column))
								{
										error("Error while checking column","Column does not exist");
								}
								else
								{
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										$explode = explode("---///---~~~",$read);
										$count_explode = count($explode);
										$return = array();
										$count = 0;
										for($i=0;$i<=$count_explode-1;$i++)
										{
												$explode[$i] = trim($explode[$i]);
												if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]\s?=\s?'(.*)'/",$explode[$i],$match))
												{
														$value = $match[2];
														array_push($return,$value);														
												}												
										}
										return($return);
								}
						}
				}
		}
}

function sdb_count_column_with_value($table,$column,$value)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column) || !isset($value))
				{
						error("Error while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								if(!sdb_column_exist($table,$column))
								{
										error("Error while checking column","Column does not exist");
								}
								else
								{
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										$explode = explode("---///---~~~",$read);
										$count_explode = count($explode);
										$count = 0;
										for($i=0;$i<=$count_explode-1;$i++)
										{
												$explode[$i] = trim($explode[$i]);
												if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]\s?=\s?'".$value."'/",$explode[$i],$match))
												{
														$count++;
												}												
										}
										return($count);
								}
						}
				}
		}
}

function sdb_where_is_my_value($table,$column,$value)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column) || !isset($value))
				{
						error("Error while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								if(!sdb_column_exist($table,$column))
								{
										error("Error while checking column","Column does not exist");
								}
								else
								{
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										$explode = explode("---///---~~~",$read);
										$count_explode = count($explode);
										$return = array();
										$count = 0;
										for($i=0;$i<=$count_explode-1;$i++)
										{
												$explode[$i] = trim($explode[$i]);
												if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]\s?=\s?'".$value."'/",$explode[$i],$match))
												{
														array_push($return,$value);
												}												
										}
										return($return);
								}
						}
				}
		}
}

function sdb_count_table_column($table,$column)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column))
				{
						error("Error while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								if(!sdb_column_exist($table,$column))
								{
										error("Error while checking column","Column does not exist");
								}
								else
								{
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										$explode = explode("---///---~~~",$read);
										$count_explode = count($explode);
										$return = 0;
										for($i=0;$i<=$count_explode-1;$i++)
										{
												$explode[$i] = trim($explode[$i]);
												if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]\s?=\s?'(.*)'/",$explode[$i],$match))
												{
														$return++;
												}												
										}
										return $return;
								}
						}
				}
		}
}

function sdb_get_column_value($table,$column)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column))
				{
						error("Error while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								if(!sdb_column_exist($table,$column))
								{
										error("Error while checking column","Column does not exist");
								}
								else
								{
										chmod($gbl_db.".minus", 0700);
										$txt=fopen($gbl_db.".minus","a+");
										$read = file_get_contents($gbl_db.".minus");
										if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]/",$read,$match))
										{
												return $match[1];
										}
										else
										{
												return FALSE;
										}
								}
						}
				}
		}
}

function sdb_get_table_column($table)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error while getting column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table))
				{
						error("Error while getting column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								chmod($gbl_db.".minus", 0700);
								$txt=fopen($gbl_db.".minus","a+");
								$read = file_get_contents($gbl_db.".minus");
								$explode = explode("---///---~~~",$read);
								$count_explode = count($explode);
								$join = array();
								for($i=0;$i<=$count_explode-1;$i++)
								{
										$explode[$i] = trim($explode[$i]);
										if(preg_match("/^table\['".$table."'\]\['(.*?)'\]\['(.*?)'\]$/",$explode[$i],$return_match))
										{
												array_push($join,$return_match[1]);
										}
								}
								return($join);
						}
				}
		}
}

function sdb_column_exist($table,$column)
{
		global $gbl_db;
		if(!isset($gbl_db))								
		{
				error("Error in while checking column","Database has not been selected yet");
		}
		else
		{
				if(!isset($table) || !isset($column))
				{
						error("Error in while checking column","Invalid parameter"); 
				}
				else
				{
						if(!sdb_table_exist($table))
						{
								error("Error while checking column","Table does not exist");				
						}
						else
						{
								chmod($gbl_db.".minus", 0700);
								$txt=fopen($gbl_db.".minus","a+");
								$read = file_get_contents($gbl_db.".minus");
								if(preg_match("/table\['".$table."'\]\['".$column."'\]\['(.*?)'\]/",$read))
								{
										$column_exist = TRUE;
								}
								else
								{
										$column_exist = FALSE;
								}
						}		
				}
		}
		return $column_exist;			
}


function sdb_drop_table($table)
{
		global $gbl_db;
		
		if(!isset($gbl_db))								
		{
				error("Error while dropping","Database has not been selected yet");
		}
		else
		{
				if(!isset($table))
				{
						error("Error while dropping table","Invalid parameter"); 
				}
				else
				{
						chmod($gbl_db.".minus", 0700);
						$txt=fopen($gbl_db.".minus","a+");
						$read = file_get_contents($gbl_db.".minus");
						
						$explode = explode("---///---~~~",$read);
						$count_explode = count($explode);
						$join_read = "";
						for($i=0;$i<=$count_explode-2;$i++)
						{
								$explode[$i] = trim($explode[$i]);
								if(preg_match("/table\['".$table."'\]\['(.*?)'\]\['(.*?)'\]\s?=\s?(.*)/",$explode[$i]) || preg_match("/table\['".$table."'\]\['(.*?)'\]\['(.*?)'\]/",$explode[$i]))
								{
										$join_read .= "";
								}
								else
								{
										$join_read .= $explode[$i]."---///---~~~";
								}
						}
						echo $join_read;
						
				}
		}
}

function sdb_db_size($db)
{
				if(!isset($db))
				{						
						error("Error while Checking Database","Invalid parameter"); 
				}
				else
				{
						
								if(sdb_db_exist($db))
								{
										$dbname = $db.".minus";
										
										$filesize = filesize($dbname);
										$bits = round($filesize/0.125,1);
										$byte = round($filesize/1,1);
										$mega = round($filesize/131072,1);
										$kilo = round($filesize/1024,1);
										
										$return = array(
												   "bits" => $bits,
												   "byte" => $byte,
												   "kilobyte" => $kilo,
												   "megabyte" => $mega
												   );
										
										return($return);
								}
								else
								{
										error("Error while Checking Database","Database does not exist");
								}
						
						
				}
}

function sdb_db_exist($db)
{
			if(!isset($db))
			{						
					error("Error while Checking Database","Invalid parameter"); 
			}
			else
			{
					if(file_exists($db.".minus"))
					{
							$val = TRUE;
					}
					else
					{
							$val = FALSE;
					}
			}
			return $val;
}

function remove_both($string)
{
		if(!isset($string))
		{
				error("Error while removing","Invalid parameter");
		}
		else
		{	
				$first = str_split($string);
				$count_first = count($first);
				$last = "";
				for($i=0;$i<=$count_first-1;$i++)
				{
						if($i > 0 && $i < $count_first-1)
						{
								$last.=$first[$i];
						}
				}
				return $last;
		}
}

function remove_last($string)
{
		if(!isset($string))
		{
				error("Error while removing","Invalid parameter");
		}
		else
		{	
				$first = str_split($string);
				$count_first = count($first);
				$last = "";
				for($i=0;$i<=$count_first-1;$i++)
				{
						if($i < $count_first-1)
						{
								$last.=$first[$i];
						}
				}
				return $last;
		}
}

function remove_first($string)
{
		if(!isset($string))
		{
				error("Error while removing","Invalid parameter");
		}
		else
		{	
				$first = str_split($string);
				$count_first = count($first);
				$last = "";
				for($i=0;$i<=$count_first-1;$i++)
				{
						if($i > 0)
						{
								$last.=$first[$i];
						}
				}
				return $last;
		}
}

function sdb_table_exist($table)
{
		global $gbl_db;
		
		if(!isset($gbl_db))								
		{
				error("Error while checking table","Database has not been selected yet");
		}
		else
		{
				if(!isset($table))
				{
						error("Error while checking table","Invalid parameter");
				}
				else
				{
						chmod($gbl_db.".minus", 0700);
						$txt=fopen($gbl_db.".minus","a+");
						$read = file_get_contents($gbl_db.".minus");						
						
								if(preg_match("/table\['".$table."'\]\['(.*?)'\]\['(.*?)'\]/",$read))
								{
										$table_exist = TRUE;
								}
								else
								{
										$table_exist = FALSE;
								}
						
				}
		}
		return $table_exist;
}

function sdb_real_escape_string($string)
{
		if(!isset($string))
		{
						error("Error while escaping from string","Invalid parameter"); 
		}
		else
		{
					$string = trim($string);
					if($string == "")
					{
							error("Error while escaping from string","String is empty");
					}
					else
					{
							$split_string = str_split($string);
							$str_count = count($split_string);
							
							$ret_string = "";
							
							for($i=0;$i<=$str_count-1;$i++)
							{
									if($split_string[$i] == '"')
									{
											$split_string[$i] = '\"';
											$ret_string .= $split_string[$i];
									}
									elseif($split_string[$i] == "'")
									{
											$split_string[$i] = "\'";
											$ret_string .= $split_string[$i];
									}
									elseif($split_string[$i] == "=")
									{
											$split_string[$i] = "\=";
											$ret_string .= $split_string[$i];
									}
									elseif($split_string[$i] == ",")
									{
											$split_string[$i] = "\,";
											$ret_string .= $split_string[$i];
									}
									elseif($split_string[$i] == "~")
									{
											$split_string[$i] = "\~";
											$ret_string .= $split_string[$i];
									}
									elseif($split_string[$i] == "\\")
									{
											$split_string[$i] = "\\\\";
											$ret_string .= $split_string[$i];
									}
									else
									{
											$ret_string .= $split_string[$i];
									}
							}
							
					}
		}
		return $ret_string;
}

function sdb_close_db()
{
		global $gbl_db;
		
		if(!isset($gbl_db))								
		{
				error("Error while closing database","Database has not been selected yet");
		}
		else
		{				
				
						$txt=fopen($gbl_db.".minus","a+");
						chmod($gbl_db.".minus", 0100);
						fclose($txt);
				
		}
}

?>