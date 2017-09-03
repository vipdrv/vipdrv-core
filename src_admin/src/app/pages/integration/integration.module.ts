import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { IntegrationComponent } from './integration.component';
import { routing } from './integration.routing';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing
    ],
    declarations: [
        IntegrationComponent
    ],
    providers: []
})
export class IntegrationModule {}
