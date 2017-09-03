import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { LeadsComponent } from './leads.component';
import { routing } from './leads.routing';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing
    ],
    declarations: [
        LeadsComponent
    ],
    providers: []
})
export class LeadsModule {}
