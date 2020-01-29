import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {

  constructor(public usrSer:UserService) { }
  public allowModules:any;

  ngOnInit() {
    this.allowModules = this.usrSer.currentUserDetails();
    console.log(this.allowModules);
  }

}
