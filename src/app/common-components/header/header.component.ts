import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  constructor(public usrSer:UserService) { }
public user_details:any; 
  ngOnInit() {
    this.user_details = this.usrSer.currentUserDetails();
    console.log(this.user_details);
  }

  logout(){
    this.usrSer.logoutUser();
  }

}
