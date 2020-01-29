import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';

@Component({
  selector: 'app-user-profile',
  templateUrl: './user-profile.component.html',
  styleUrls: ['./user-profile.component.css']
})
export class UserProfileComponent implements OnInit {

  constructor(public usrSer:UserService) { }
  public fields:any;
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
        this.fields = cntFlds;
        console.log(cntFlds);
      },
      err=>console.log(err)
    )
  }

}
