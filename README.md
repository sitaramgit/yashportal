 

	Angular Customer Portal Setup
	
	step - 1
1	AngularSoap' folder and 'AngularAPI.php' keep in crm root
2	go to the 'AngularSoap > soap.php' in this file change the Server path
	ex : $Server_Path = "http://173.255.216.217/yash71/";
	
	step - 2
1	go to the 'soap' folder 
2	override customerportal.php file with our file
	
	step 3
1	go to the angular "src\app\common-services\user.service.ts"
2	find serverUrl variable.

3	give ther your crm AngularApi.php path.

	ex :  public serverUrl = "http://app.theprintshop.co.za/AngularAPI.php";
4	after run command to buld angular portal
	ex : CMD : ng build --prod --base-href="http://app.theprintshop.co.za/customerportal/"