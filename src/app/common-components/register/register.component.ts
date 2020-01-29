import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  constructor(public usrSer:UserService, public route:Router) { }
  public errMsg:any = false;
  public loder = false;
  ngOnInit() {
  }

  submitRegForm(frm){ 
    this.loder = true;
    this.usrSer.createUser(frm).subscribe(
      data =>{
        this.loder = false;
        if(data[0].status){
          this.route.navigate(['/login'], { queryParams: { status: 'created'} });
        }else{
          this.errMsg = data[0].msg;
        }
      },
      err => console.log(err)
    )
  }

}
