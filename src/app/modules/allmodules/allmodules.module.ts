import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AllmodulesRoutingModule } from './allmodules-routing.module';
import { AllmodulesComponent } from './allmodules.component';
import { ModuleListComponent } from './module-list/module-list.component';
import { ModuleDetailComponent } from './module-detail/module-detail.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { TicketCreateComponent } from './ticket-create/ticket-create.component';


@NgModule({
  declarations: [AllmodulesComponent, ModuleListComponent, ModuleDetailComponent, TicketCreateComponent],
  imports: [
    CommonModule,
    HttpClientModule,
    FormsModule,
    AllmodulesRoutingModule
  ],
  bootstrap:[AllmodulesComponent]
})
export class AllmodulesModule { }
