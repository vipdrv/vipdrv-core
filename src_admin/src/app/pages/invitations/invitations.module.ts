import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { PaginationModule } from 'ng2-bootstrap';
import { BusyModule } from 'angular2-busy';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { InvitationsComponent } from './invitations.component';
import { InvitationDetailsInfoComponent } from './details/info/invitationDetailsInfo.component';
import { InvitationsTableComponent } from './table/invitationsTable.component';
import { InvitationDetailsCreateComponent } from './details/create/invitationDetailsCreate.component';
import { routing } from './invitations.routing';
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
    declarations: [
        InvitationsComponent,
        InvitationDetailsInfoComponent,
        InvitationsTableComponent,
        InvitationDetailsCreateComponent
    ],
    exports: [
        InvitationsComponent,
        InvitationDetailsInfoComponent,
        InvitationsTableComponent,
        InvitationDetailsCreateComponent
    ],
    providers: []
})
export class InvitationsModule {}