import { Component } from '@angular/core';
import { UserService } from './common-services/user.service';
import { Router, NavigationEnd } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'yash';
  constructor(public usrSer:UserService,private router: Router){}

  public user:boolean;
  ngOnInit(){

    this.usrSer.authUser.subscribe(
      logged => {
        this.user = logged; 
      }
    );

    this.router.events.subscribe(val => {
   
      
      if (val instanceof NavigationEnd) {
        console.log(val.url);
        window.scrollTo(0, 0);
        
      }
  });

   
  }
}
