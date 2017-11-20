import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { PaginationModule } from 'ng2-bootstrap';
import { BusyModule } from 'angular2-busy';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { LeadsTableComponent } from './table/leadsTable.component';
import { LeadDetailsInfoComponent } from './details/info/leadDetailsInfo.component';
import { LeadsComponent } from './leads.component';
import { routing } from './leads.routing';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        Ng2Bs3ModalModule,
        PaginationModule,
        BusyModule
    ],
    exports: [
        LeadsComponent,
        LeadsTableComponent,
        LeadDetailsInfoComponent
    ],
    declarations: [
        LeadsComponent,
        LeadsTableComponent,
        LeadDetailsInfoComponent
    ],
    providers: []
})
export class LeadsModule {}
