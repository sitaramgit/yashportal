import { Injectable } from '@angular/core';
import { Observable, BehaviorSubject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { SharedService } from './shared.service';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(private http:HttpClient, private share:SharedService,private route:Router) { }

  public serverUrl = "http://173.255.216.217/yash71/AngularAPI.php";
  public decoded = window.atob("encoded");
  loginUser(data):Observable<any>{
    let params={module:"user",view:"check", email : data.email, password : data.password };
    return this.http.post(this.serverUrl,params);
  }

  createUser(data):Observable<any>{
    return this.http.post(this.serverUrl,data);
  }

  private userLogged = new BehaviorSubject(!!localStorage.getItem('user_details'));
  public authUser = this.userLogged.asObservable();

  changeUserStatus(data: boolean) {
    this.userLogged.next(data);
   
  }
 
  loggedIn(){
    return  !! localStorage.getItem('user_details');
   }

   currentUserDetails(){
    let usr = window.atob(localStorage.getItem('user_details'));
    return JSON.parse(usr);
  }

  logoutUser(){
    this.changeUserStatus(false);
    let params={module:"logout",view:"user",id:this.currentUserDetails().id, sessionId:this.currentUserDetails().sessionid};
    this.http.post(this.serverUrl,params).subscribe(
      (data)=>{
        console.log(data);          
      },
      err => console.log(err)
    )
    
    localStorage.removeItem('user_details');
    localStorage.clear(); 
    this.route.navigate(["/login"]);
  }

  forgot_Password(data):Observable<any>{
    return this.http.post(this.serverUrl,data);
  }

  getUserDetails():Observable<any>{
    
    let params={module:"user",view:"details", record:this.currentUserDetails().id};
    return this.http.post(this.serverUrl,params);
  }

  updateUser(data):Observable<any>{
    return this.http.post(this.serverUrl,data);
  }
}
