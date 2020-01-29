import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {

  constructor(private usrSer:UserService, private route:Router) { }
  public errMsg:any = false;
  public loder:boolean = false;
  public btnDsbl = false;
  ngOnInit() {
  }

  forgotpassword(val){
    this.btnDsbl = true;
    this.errMsg = false;
    this.loder = true;
    this.usrSer.forgot_Password(val).subscribe(
      (data)=>{
        this.errMsg = data;
       let res = data.split("@@@");
        if(res[0] == "true"){ 
          this.errMsg = "";
          this.route.navigate(['/login'], { queryParams: { forgot: 'mail_sent' } })
        }else{
          this.errMsg = res[1];
        }
        this.btnDsbl = false;
      },
      err => console.log(err),
      () => this.loder = false
    )
  }
 

}
