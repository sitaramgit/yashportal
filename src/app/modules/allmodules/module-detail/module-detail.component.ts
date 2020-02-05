import { Component, OnInit } from '@angular/core';
import { GetModulesService } from '../get-modules.service';
import { ActivatedRoute, Router, ParamMap } from '@angular/router';
import { UserService } from 'src/app/common-services/user.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-module-detail',
  templateUrl: './module-detail.component.html',
  styleUrls: ['./module-detail.component.css']
})
export class ModuleDetailComponent implements OnInit {

  constructor(public http:HttpClient, public allSer:GetModulesService, public actroute:ActivatedRoute, public route:Router, public usrSer:UserService) { }
  public loder = false;
  public moduleName:any;
  public record:any;

  public lables:any;
  public listVals:any = null;
  public contents:any = null;
  public cmnt:any = "";
  ngOnInit() {  
    this.actroute.paramMap.subscribe((params: ParamMap) => {
      this.moduleName  = params.get('modulename');
      this.record  = params.get('id');
      this.moduleDetail(this.moduleName,this.record)
      });
  }

  moduleDetail(modul,id){
    
    this.loder = true;
    this.allSer.getModuleDetail(modul,id).subscribe(
      data=>{
        this.loder = false; 
          // this.listVals = data[0][this.moduleName];
          this.contents = data.html;
          if(data.files != null){
            this.files =data.files;
          }

          console.log( this.listVals); 
        
        
      },
      err => console.log(err)
    );
  }
public files:any;
  addCmnt(){
    console.log(this.cmnt)
    this.loder = true;
    this.allSer.createComment(this.moduleName,this.record,this.cmnt).subscribe(
      data=>{ 
          console.log(data); 
          
          this.cmnt = ""; 
          this.moduleDetail(this.moduleName,this.record);
      },
      err => console.log(err)
    );
  }

public selectedFile:any;
  onSelectedFile(event){
    this.selectedFile = event.target.files[0];
    this.selectedFile.module = 'documents';
    const file = event.target.files[0]; 
    console.log(this.selectedFile);
    console.log(event);
    }


    onSubmit(){

      if(this.selectedFile!=null){
        this.loder = true;
        const formData = new FormData();
        formData.append('name', this.selectedFile.name);
        formData.append('document', this.selectedFile);
        formData.append('ticket_id', this.record);
        formData.append('contact_id', this.usrSer.currentUserDetails().id);
        formData.append('sessionid', this.usrSer.currentUserDetails().sessionid);
        console.log(formData);
        // console.log(this.profileForm.value);
      this.http.post(this.usrSer.serverUrl,formData).subscribe(
        data=>{
          console.log(data)
          this.files = data;
          this.loder = false;
        },
        err => console.log(err)
      )
       
      } 
      }

}
