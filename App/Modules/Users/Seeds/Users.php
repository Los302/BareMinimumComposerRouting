<?php
include '../../../../includes/initialize.php';

$seeder = new tebazil\dbseeder\Seeder($GLOBALS['DB'][0]);
$generator = $seeder->getGeneratorConfigurator();
$faker = $generator->getFakerConfigurator();

$LoremIpsum = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur tincidunt tempus mauris vel viverra. Nam semper, nisl lobortis condimentum fermentum, tellus ligula pretium neque, eget iaculis ligula neque nec ligula. Nam eu dignissim massa. Nam tortor mi, consectetur sit amet odio in, finibus ultricies eros. Nulla quis pharetra lorem. Curabitur luctus vestibulum pharetra. Curabitur sagittis tristique orci, vel pellentesque libero posuere vitae. Suspendisse laoreet massa quis purus hendrerit, vitae dictum justo tristique. Duis leo lorem, venenatis at fringilla in, pellentesque sed tellus. Pellentesque et diam imperdiet, mollis diam nec, tincidunt erat. Nam sodales non turpis interdum ultrices. Duis at pulvinar ipsum, at elementum mauris. Praesent luctus dui quis risus vestibulum ultrices. Suspendisse id neque sodales, malesuada sapien ac, porttitor leo. Aliquam ut efficitur ex, at malesuada sapien.
Ut ut porttitor leo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed a vulputate neque. Nunc porta imperdiet purus sed porta. Etiam at ex mauris. Morbi lacus tellus, congue et laoreet eget, ullamcorper vel eros. Pellentesque metus tortor, efficitur nec turpis ac, placerat egestas odio. Donec elementum, libero id auctor tempor, ante felis accumsan neque, a rhoncus nulla nibh id ex. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla sit amet sollicitudin tellus, at cursus turpis. Cras eu suscipit lorem.
Sed maximus enim quis felis tincidunt iaculis. Aenean venenatis pellentesque ipsum, eget blandit ante. Duis commodo enim in mollis tincidunt. Pellentesque luctus ipsum ac sollicitudin molestie. Suspendisse et venenatis dolor. Quisque eu ultrices eros. Nunc bibendum erat dui, rutrum maximus dui dictum in. Curabitur ultrices diam et nibh commodo dignissim. Aenean tincidunt, arcu sit amet mattis feugiat, diam ex fermentum neque, quis elementum dui lectus sed orci. Etiam quis egestas dolor.
Maecenas lectus felis, eleifend a ante non, consectetur auctor massa. Phasellus finibus, neque sit amet faucibus interdum, justo diam posuere neque, eget posuere elit ex sit amet justo. Etiam porttitor condimentum tempor. Nam tincidunt aliquet accumsan. Quisque scelerisque porta augue. Maecenas in mauris feugiat, vehicula turpis vitae, dapibus orci. Quisque vitae ornare purus. Sed ullamcorper molestie velit, in euismod enim elementum ut. Mauris ornare mi ut magna ornare, non eleifend massa viverra. Suspendisse ultricies dui consectetur risus consequat, id efficitur ligula egestas. Mauris at lorem tempor, posuere ex eu, fringilla justo. Sed nec diam non neque convallis bibendum ut sit amet dui.
Nunc velit nibh, auctor eget porttitor vitae, iaculis vehicula ipsum. Aenean cursus cursus efficitur. Mauris et nisl auctor, elementum nisl et, lobortis sapien. Curabitur enim leo, maximus et dolor sit amet, cursus accumsan erat. Phasellus vehicula euismod tortor, nec maximus nisl fermentum id. Morbi sit amet lectus aliquet, dapibus mi id, condimentum metus. Nam ultricies, enim in hendrerit malesuada, lacus dui tempus ipsum, ut ultrices felis nunc quis lacus. Aliquam eu sapien eu magna pharetra blandit. Mauris tincidunt dui diam, sed pulvinar erat pharetra id. Proin maximus consequat cursus. Nulla facilisi. Duis sodales, tellus nec congue imperdiet, nibh dolor consequat est, eu consectetur tellus tortor ac diam. Ut a vestibulum dui, non ornare diam. Ut feugiat cursus risus, eget fermentum sem gravida at. In sollicitudin non purus vitae vestibulum.';

$UsersVals = [
    [
        'id' => 1,
        'username' => 'Admin',
        'password' => 'test1234',
        'email' => 'Carlos@LosPrograms.com',
        'role' => '|ADMIN|USER|',
        'active' => 1
    ]
];
$seeder->table('users')->data($UsersVals);

$seeder->refill();
