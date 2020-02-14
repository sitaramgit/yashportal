import { Component, OnInit } from '@angular/core';
import { UserService } from 'src/app/common-services/user.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {

  constructor(public usrSer:UserService) { }

  ngOnInit() {
    this.dashDtls();
  }

  public dashData:any;
  dashDtls(){
    this.usrSer.dashDetails().subscribe(
      data=>{
        console.log(data)
        this.dashData = data[0]
      },
      err=>console.log(err)
    )
  }

}
