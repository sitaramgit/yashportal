import { Component, OnInit } from '@angular/core';
import { GetModulesService } from '../get-modules.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-ticket-create',
  templateUrl: './ticket-create.component.html',
  styleUrls: ['./ticket-create.component.css']
})
export class TicketCreateComponent implements OnInit {

  constructor(public geSer:GetModulesService, public route:Router) { }

  ngOnInit() {
    this.comboValues();
  }
public loder = true;
public productid;
public productname;
public ticketpriorities;
public ticketseverities;
public ticketcategories;
public serviceid;
public servicename;
  comboValues(){
    this.geSer.getComboValues().subscribe(
      data=>{
        console.log(data);
        this.loder = false;
        this.productid = data.productslist[0];
        this.productname = data.productsname[0];
        this.ticketpriorities = data.ticketpriorities[0];
        this.ticketseverities = data.ticketseverities[0];
        this.ticketcategories = data.ticketcategories[0];
        this.serviceid = data.serviceid[0];
        this.servicename = data.servicename[0];
      },
      err => console.log(err)
    )
  }

  createTkt(frm){
    console.log(frm);
    this.loder = true;
    this.geSer.createTicket(frm).subscribe(
      data=>{
        console.log(data[0].new_ticket.ticketid)
        this.route.navigate(['/all-modules/module-detail',"HelpDesk",data[0].new_ticket.ticketid]);
      },
      err => console.log(err)
    )
  }
}
