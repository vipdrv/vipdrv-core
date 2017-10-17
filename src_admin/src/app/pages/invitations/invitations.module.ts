import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { InvitationsComponent } from './invitations.component';
import { routing } from './invitations.routing';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing
    ],
    declarations: [
        InvitationsComponent
    ],
    providers: []
})
export class InvitationsModule {}