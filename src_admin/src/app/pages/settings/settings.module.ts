import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { BusyModule } from 'angular2-busy';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { SettingsComponent } from './settings.component';
import { routing } from './settings.routing';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        BusyModule
    ],
    declarations: [
        SettingsComponent
    ],
    exports: [
        SettingsComponent
    ],
    providers: []
})
export class SettingsModule {}