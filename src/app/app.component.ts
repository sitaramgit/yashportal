import { Component } from '@angular/core';
import { UserService } from './common-services/user.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'yash';
  constructor(public usrSer:UserService){}

  public user:boolean;
  ngOnInit(){

    this.usrSer.authUser.subscribe(
      logged => {
        this.user = logged; 
      }
    );
    window.scrollTo(0, 0);
  }
}
