import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  constructor(public usrSer:UserService, public actroute:ActivatedRoute,public route:Router) { }
  frmData:any = {};
  public errMsg:any = false;
  public regStatus:any = false;
  public for_pass:any = false;
  public loder:boolean = false;
  ngOnInit() {

    this.actroute.queryParams.subscribe(
      (param) =>{ 
        if(param.length !=0){
          this.regStatus = param.status != null ? true : false;
          this.for_pass = param.forgot != null ? true : false;
        }
      }
    )
  }

  loginForm(){
     this.loder = true;
    this.usrSer.loginUser(this.frmData).subscribe(
      data=>{ 
        this.loder = false;
        let usrData = data[0];
        if(usrData =="INVALID_USERNAME_OR_PASSWORD"){
          this.errMsg = "Inavalid User Name or Password"; 
          return false;
        }
        let jsnStr = JSON.stringify(usrData);
        const jsnencoded = window.btoa(jsnStr); 
        localStorage.setItem("user_details",jsnencoded);
        this.usrSer.changeUserStatus(true);
        this.route.navigate(["/dashboard"]);
      },
      err=> console.log(err)
       
    );
  }

}
