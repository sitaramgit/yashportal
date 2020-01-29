import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-edit-profile',
  templateUrl: './user-edit-profile.component.html',
  styleUrls: ['./user-edit-profile.component.css']
})
export class UserEditProfileComponent implements OnInit {

  constructor(public usrSer:UserService, public route:Router) { }
  public fields:any = {
    firstname:"",
    lastname:"",
    mobile:"",
    mailingcity:"",
    mailingstate:"",
    mailingcountry:""
  };
  public loder = false;
  ngOnInit() {
    this.user_details();
  }


  user_details(){
    this.loder = true;
    this.usrSer.getUserDetails().subscribe(
      data=>{
        // console.log(data)
        this.loder = false;
        let culmns = data[0].Contacts
        let cntFlds = {}
        culmns.filter((itm)=>{
          cntFlds[itm.fieldname] = itm.fieldvalue;
          return true;
        });
        // this.fields.firstname = cntFlds.firstname;
        this.fields = cntFlds;
        console.log(cntFlds);
      },
      err=>console.log(err)
    )
  }

  updateUser(frm){
    this.usrSer.updateUser(frm).subscribe(
      data =>{
        this.route.navigate(["/user"]);
        console.log(data)
      },
      err => console.log(err)
    )
  }

}
